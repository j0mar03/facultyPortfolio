<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DepartmentFacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map departments to course codes
        $departmentToCourses = [
            'Department Of Office Management and Information Technology' => ['DOMT', 'DIT'],
            'Department Of Electrical And Mechanical Engineering Technology' => ['DMET', 'DEET'],
        ];

        // Preload course IDs by code
        $courses = Course::whereIn('code', ['DOMT', 'DIT', 'DMET', 'DEET'])
            ->pluck('id', 'code');

        $defaultPassword = Hash::make('password');

        // DOMT / DIT faculty
        $domtDitFaculty = [
            ['name' => 'Abogado, Camilo P.', 'email' => 'cpabogado@pup.edu.ph'],
            ['name' => 'Apsay, Jonnalyn B.', 'email' => 'jbapsay@pup.edu.ph'],
            ['name' => 'Atencio, Mylo D.p.', 'email' => 'mdatencio@pup.edu.ph'],
            ['name' => 'Austria, Adrian', 'email' => 'ajdaustria@pup.edu.ph'],
            ['name' => 'Bautista, Marc Anthony S', 'email' => 'msbautista@pup.edu.ph'],
            ['name' => 'Belista, Paula Grace Ann', 'email' => 'pgabbelista@pup.edu.ph'],
            ['name' => 'Bien, Maria Azalea J.', 'email' => 'majbien@pup.edu.ph'],
            ['name' => 'Blasquino, Edrian G.', 'email' => 'egblasquino@pup.edu.ph'],
            ['name' => 'Boniol, Jean O.', 'email' => 'joboniol@pup.edu.ph'],
            ['name' => 'Crasco, Melania Melanie M', 'email' => 'mmmcrasco@pup.edu.ph'],
            ['name' => 'Cruz, Regie', 'email' => 'rscruz@pup.edu.ph'],
            ['name' => 'Dela Isla, Jonard John M.', 'email' => 'jjmdelaisla@pup.edu.ph'],
            ['name' => 'Dela Isla, Josephine', 'email' => 'jmdelaisla@pup.edu.ph'],
            ['name' => 'Esparas, Natividad Taduan', 'email' => 'ntesparas@pup.edu.ph'],
            ['name' => 'Huceña, Maria Aida B.', 'email' => 'mabhucena@pup.edu.ph'],
            ['name' => 'Lacson, Marigel Nicholle', 'email' => 'mnmlacson@pup.edu.ph'],
            ['name' => 'Lim, Gina S.', 'email' => 'gslim@pup.edu.ph'],
            ['name' => 'Lipardo Jr., Fernando V.', 'email' => 'fvlipardojr@pup.edu.ph'],
            ['name' => 'Macapagal, Diana M.', 'email' => 'dmmacapagal@pup.edu.ph'],
            ['name' => 'Padilla, Jannet S.', 'email' => 'jspadilla@pup.edu.ph'],
            ['name' => 'Payra, Marisol', 'email' => 'mdpayra@pup.edu.ph'],
            ['name' => 'Reyes, Nino', 'email' => 'ncreyes@pup.edu.ph'],
            ['name' => 'Salamatin, Ofelia', 'email' => 'odsalamatin@pup.edu.ph'],
            ['name' => 'Salazar, Raquel G.', 'email' => 'rgsalazar@pup.edu.ph'],
            // Note: Simacon Rein Ryan uses gmail, skip non-institutional for now
            ['name' => 'Solosa, Ma. Joepe V.', 'email' => 'mjasolosa@pup.edu.ph'],
            ['name' => 'Villegas, May Rose M.', 'email' => 'mrmvillegas@pup.edu.ph'],
        ];

        // DMET / DEET faculty
        $dmetDeetFaculty = [
            ['name' => 'Amarille, John Arnie', 'email' => 'janamarille@pup.edu.ph'],
            ['name' => 'Amul, Mark Anthony Q.', 'email' => 'maqamul@pup.edu.ph'],
            ['name' => 'Cabrillas, Victor Alfredo C.', 'email' => 'vaccabrillas@pup.edu.ph'],
            ['name' => 'Dacles, Jomar J.', 'email' => 'jjdacles@pup.edu.ph'],
            ['name' => 'David, Aina M.', 'email' => 'amdavid@pup.edu.ph'],
            ['name' => 'Evangelista, Arturo P.', 'email' => 'apevangelista@pup.edu.ph'],
            // Skip non-institutional gmail for now (Vic Joseph, Andrei)
            ['name' => 'Forio, Arjay', 'email' => 'arforio@pup.edu.ph'],
            ['name' => 'Glori, Jonathan', 'email' => 'jdglori@pup.edu.ph'],
            ['name' => 'Lacdang, Clint Michael', 'email' => 'cmflacdang@pup.edu.ph'],
            ['name' => 'Legaspi, Jefferson N.', 'email' => 'jnlegaspi@pup.edu.ph'],
            ['name' => 'Limkian, Jason D.', 'email' => 'jdlimkian@pup.edu.ph'],
            ['name' => 'Marcaida, Jay Ar D.', 'email' => 'jadmarcaida@pup.edu.ph'],
            ['name' => 'Santos, Pablo', 'email' => 'prtsantos@pup.edu.ph'],
            ['name' => 'Tindogan, Paulo E.', 'email' => 'petindogan@pup.edu.ph'],
        ];

        // Helper to create faculty and attach primary course
        $createFaculty = function (array $people, array $courseCodes) use ($courses, $defaultPassword) {
            foreach ($people as $person) {
                // Only process if email looks valid
                if (empty($person['email']) || ! filter_var($person['email'], FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $user = User::updateOrCreate(
                    ['email' => $person['email']],
                    [
                        'name' => $person['name'],
                        'password' => $defaultPassword,
                        'role' => 'faculty',
                    ]
                );

                // Attach primary course_id based on first code in list
                foreach ($courseCodes as $code) {
                    if (isset($courses[$code])) {
                        $user->course_id = $courses[$code];
                        $user->save();
                        break;
                    }
                }
            }
        };

        // DOMT/DIT faculty → attach primary course as DOMT
        $createFaculty($domtDitFaculty, ['DOMT', 'DIT']);

        // DMET/DEET faculty → attach primary course as DMET
        $createFaculty($dmetDeetFaculty, ['DMET', 'DEET']);

        $this->command?->info('Department faculty seeded successfully for DOMT/DIT and DMET/DEET.');
    }
}

