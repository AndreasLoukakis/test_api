<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Monolog\Logger as Monolog;
use Monolog\Formatter\LineFormatter;
use Illuminate\Log\Writer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseConfigureLogging;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Report;
use App\Vessel;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $filters = $request->all();
        $validation = $this->validateFilters( $filters );
        
        if ($validation->fails()) {

            $this->makeLogEntry('ERROR', $request,  json_encode($validation->errors()));

            return json_encode($validation->errors());
        }

        //query for results
        $reports = $this->queryReports($filters);

        $this->makeLogEntry('INFO', $request,  json_encode($reports));

        return json_encode($reports);

    }

    private function queryReports( $filters ) {

        $reports = Report::with('vessel');
        
        //build the query with filters
        if ( isset($filters['imo']) ) {
            $reports = $reports->whereIn('imo', explode(',', $filters['imo']) );
        }

        if ( isset($filters['condition']) ) {
            $reports = $reports->where('conditionType', $filters['condition']);
        }

        if ( isset($filters['fuel_consumption']) ) {

            $parts = explode(',', $filters['fuel_consumption']);
            $sign = $parts[0] == 'gt' ? '>' : '<';
            $val = $parts[1];
            $type = $parts[2] . 'Cons';

            $reports = $reports->where( $type, $sign, $val);
        }

        if ( isset($filters['from']) ) {
            $reports = $reports->where( 'created_on', '>=', $filters['from']);
        }

        if ( isset($filters['to']) ) {
            $reports = $reports->where( 'created_on', '<=', $filters['to']);
        }

        $result = json_encode($reports->get());

        return isset($filters['format']) &&  $filters['format'] == 'csv'
            ? $this->toCSV($result)
            : $result;

    }

    private function validateFilters( $input ) {

        //custom validation rules
        //imo should only contain comma seperated numbers
        \Validator::extend('digitsOrComma', function($attr, $val, $params, $validation) {
            $parts = explode(',', $val);
            foreach ($parts as $part) {
                if (!ctype_digit($part)) {
                    return false;
                }
            }
            return true;
        });

        //fuel_consumption should be in the form fuel_consumption=lt or gt,value,type
        //in a production app, probably best to break these rules and return more specific messages
        \Validator::extend('fuel_consumption', function($attr, $val, $params, $validation) {
            $parts = explode(',', $val);
            if (count($parts) != 3 ||
                !in_array($parts[0], ['lt', 'gt']) ||
                !ctype_digit($parts[1]) ||
                !in_array($parts[2], ['me', 'aux'])) return false;

            return true;
        });

        //filter params validation
        $rules = [
            'imo' => 'digitsOrComma',
            'condition' => 'in:steaming,anchor',//enum field
            'format' => 'in:csv,json',
            'from' => 'date|date_format:Y-m-d H:i:s',
            'to' => 'date|date_format:Y-m-d H:i:s',
            'fuel_consumption' => 'fuel_consumption',
        ];

        $messages = [
            'imo.digitsOrComma' => 'imo should only contain comma seperated numbers',
            'condition.in' => 'Condition only accepts steaming or anchor as values',
            'format.in' => 'Response can be formated either in csv or json',
            'datetime_frame.*' => 'datetime_frame should be in the form: ...&datetime_frame=fromdate,todate',
            'fuel_consumption.fuel_consumption' => 'fuel_consumption should be in the form: ...&fuel_consumption=lt or gt,number, me or aux'
        ];

        return \Validator::make($input, $rules, $messages);

    }

    private function makeLogEntry($type, $request, $message ) {

        $logpath = storage_path() . '/logs/DM_API.log';
        $logLevel = $type == 'INFO' ? Monolog::INFO : Monolog::ERROR;
        $logStreamHandler = new StreamHandler($logpath, $logLevel);

        $logFormat = "%datetime% [%level_name%] (%channel%): %message% %context% %extra%\n";
        $formatter = new LineFormatter($logFormat);
        $logStreamHandler->setFormatter($formatter);
        
        $logger = new Monolog('DM_API');
        $logger->pushHandler($logStreamHandler);

        $type == 'INFO'
            ? $logger->info('Request:'.$request->getRequestUri().' IP: '.$request->getClientIp().' Response: '.$message)
            : $logger->error('Request:'.$request->getRequestUri().' IP: '.$request->getClientIp().' Response: '.$message);

    }

    private function toCSV ($reports) {

        $data = json_decode($reports);
        $result = [];
        foreach($data as $row) {
            //flaten array
            $rowdata = [];
            foreach($row as $item) {
                $rowdata = array_merge($rowdata, (array)$item);
            }
            $result[] = $rowdata;
        }

        $headers = [   
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=reports.csv'
        ];

        $callback = function() use ($result) {

            $FH = fopen('php://output', 'w');
            foreach ($result as $row) { 
                fputcsv($FH, $row);
            }

            fclose($FH);

        };

        $response = new StreamedResponse($callback, 200, $headers);

        return $response->send();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
