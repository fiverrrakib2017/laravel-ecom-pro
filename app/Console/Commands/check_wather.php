<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class check_wather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check_wather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $city = 'Cumilla';
        $apiKey = env('WEATHER_API_KEY');
        $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";

        $response = Http::get($url);
        $weather = $response->json();

        $status = strtolower($weather['weather'][0]['main']); // like 'Rain', 'Thunderstorm', etc.

        if (in_array($status, ['rain', 'thunderstorm', 'drizzle'])) {
            $customers = Customer::where('status', 1)->get();

            foreach ($customers as $customer) {
                $msg = 'প্রিয় গ্রাহক, বর্তমানে আবহাওয়া খারাপ থাকায় আমাদের সার্ভিসে কিছুটা বিঘ্ন ঘটতে পারে। ধন্যবাদ - ';

                /*SMS Gateway API call*/
                Http::post('https://your-sms-api.com/send', [
                    'number' => $customer->phone,
                    'message' => $msg,
                ]);
            }

            //Log::info('Weather Alert sent to all customers.');
        } else {
            //Log::info("Weather is fine: $status");
        }
    }
}
