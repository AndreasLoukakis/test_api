<?php

use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = app_path() . '/../reports.csv';
        
        if (($handle = fopen($file, "r")) !== FALSE) {

            $row = fgetcsv($handle, 0 , ';');
            while (($row = fgetcsv($handle, 0 , ';')) !== FALSE) {
                DB::table('reports')->insert([
                    'imo' => $row[0],
                    'created_on' => $row[1],
                    'conditionType' => $row[2],
                    'meHours' => $this->normalizeFloat($row[3]),
                    'meCons' => $this->normalizeFloat($row[4]),
                    'auxHours' => $this->normalizeFloat($row[5]),
                    'auxCons' => $this->normalizeFloat($row[6]),
                    'observedDistance' => $this->normalizeFloat($row[7]),
                ]);
            }
            fclose($handle);
        }
    }

    private function normalizeFloat($num) {

        return is_string($num) 
            ? str_replace(',', '.', $num)
            : number_format($num, 2, '.');

    }
}
