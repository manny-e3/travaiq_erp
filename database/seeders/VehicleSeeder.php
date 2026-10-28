<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'key' => 'escalade',
                'name' => 'Cadillac Escalade ESV',
                'class' => 'Executive SUV',
                'description' => 'The pinnacle of luxury travel. Features massive cargo room, leather captain chairs, and tri-zone climate controls.',
                'passengers' => 6,
                'luggage' => 6,
                'hourly_rate' => 150,
                'airport_rate' => 180,
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCwdQOl_gEikFr8S56LY87vmh4n4T8Os96QusQHy_xCw3e_FGw-AKH_9_HDQkXuHRIoeUKPb3R5W1duTKCiRHfkfuttg8Co3muxab46pIDiDk3EZzUpb6mo6AGoX8wqlwLM1jZrInNnZ_3I4pUNDeZ5JX-x5guIDlXsknVGldwFcpiz__GkBWfv5z1JVAlsOJBicn7YKUeeYJjp2cIcrGgBz4nygltAm9o94StJ9rg-nexFOFU51pLi-Zs9rXSrS_txEOscFDJPEL5c'
            ],
            [
                'key' => 'sclass',
                'name' => 'Mercedes-Benz S-Class',
                'class' => 'Executive Sedan',
                'description' => 'The standard by which all executive cars are measured. Supreme comfort, acoustic isolation, and rear sunshades.',
                'passengers' => 3,
                'luggage' => 3,
                'hourly_rate' => 130,
                'airport_rate' => 160,
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCsyejjO2p3XWJPueL65eaD1Dz8MPeDiPZFAxinPzwoXjq-34JFjXV7nI8gSZUvH-4H-_3MuTqr7-y6asXq76Mvy1FOa0ytqnVIEIocux9BP9jzRpsLcOPzC0CPsGRnXjIOAY6okz1GW8sz_pZKdQLxSHscgkssInfC7gqEuj_fP8P29qZCIbVmv5p1pF7JuuCTwNQAtblBzHTdTyeBtvrhNzucbDu9L-b2aJPI9T6rkM8-4e_7bhs3IOnbYqMsg2xqWsmVh5NdlJPC'
            ],
            [
                'key' => 'suburban',
                'name' => 'Chevrolet Suburban',
                'class' => 'Luxury SUV',
                'description' => 'A robust, spacious option designed for excellent passenger volume, luggage storage, and comfortable rides.',
                'passengers' => 7,
                'luggage' => 6,
                'hourly_rate' => 135,
                'airport_rate' => 165,
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkJa-KW0Zsl3aCDAH_rnmt7-1ZIaJcMSCxgkvvMggAlxNeFjf1DX4_frPaqZMjYyaGmFSpfyQjgbawq4lltJSwN6quavhUXr1VCIM4r1jIuQ1IWc6H1Gu0OSxVzfQIqmDkNxBN7Fv5bqPb400VQTSG6TWKJdSFYmAlPMS7vPE3f_ydMROu9vsLVQ_1FXkF-O5pD4fX04QxUFk61Bw5baAQ_7zDkE-NXAQD7CjoUNptchFFjwIoO6TdPjpsTVBzA-xdJX5_2YVnIr2G'
            ],
            [
                'key' => 'sprinter',
                'name' => 'Mercedes-Benz Sprinter',
                'class' => 'Executive Van',
                'description' => 'The ultimate group shuttle vehicle. Exceptional headroom, individual leather seats, and large luggage capacity.',
                'passengers' => 14,
                'luggage' => 14,
                'hourly_rate' => 180,
                'airport_rate' => 220,
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAH0Gru2JVDtgOYteoO8yQTTURBBLcatQbfZFs-fSyJjsuit7XekJGIqa86OLaZDUC_CRdr1csFeAjg-7A7CLBat7IqeXIQ9kkyk8NYKL8yJSz_1HQyeBGZriU55fSCdVPwgmsWYMjgGc-PR6aJ03aEDhuk8j9dRZoRbz6EFAGfr7_0pIhWpUkw1SuwJpakjbkZOwj3Tio4rZmMuEVs0Xq7kEbqr_GXe7MnrXfEVf-lSVi5FPcZ3jTljm2RTsTxhEIM-qvmLXHBs64o'
            ]
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(['key' => $vehicle['key']], $vehicle);
        }
    }
}
