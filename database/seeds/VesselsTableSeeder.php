<?php

use Illuminate\Database\Seeder;

class VesselsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = app_path() . '/../vessels.csv';
        
        if (($handle = fopen($file, "r")) !== FALSE) {

            $row = fgetcsv($handle, 0 , ';');
            while (($row = fgetcsv($handle, 0 , ';')) !== FALSE) {
                DB::table('vessels')->insert([
                    'name' => $row[0],
                    'imo' => $row[1],
                    'email' => $row[2],
                ]);
            }
            fclose($handle);
        }
    }
}