<?php

namespace Database\Seeders;

use App\Models\PhysicianSpeciality;
use Illuminate\Database\Seeder;

class PhysicianSpecialityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PhysicianSpeciality::truncate();

        $types =  [
            [
                'specialty_type' => 'Gynecology',
                'certification_agency' => 'American Board of Obstertrics & Gynecology',
                'agency_acronym' => 'ABOG',
                'website' => 'https://www.abog.org/',
            ],
            [
                'specialty_type' => 'Gynecology',
                'certification_agency' => 'American College of Obsterticans and Gynecologist',
                'agency_acronym' => 'ACOG',
                'website' => 'https://www.acog.org/',
            ],
            [
                'specialty_type' => 'Podiatry',
                'certification_agency' => 'American Board of Preventive Medicine',
                'agency_acronym' => 'APBM',
                'website' => 'https://www.abpmed.org/',
            ],
            [
                'specialty_type' => 'Podiatry',
                'certification_agency' => 'American Board of Foot and Ankle Surgery',
                'agency_acronym' => 'ABFAS',
                'website' => 'https://www.abfas.org/',
            ],
            [
                'specialty_type' => 'Primary Care',
                'certification_agency' => 'American Board of Family Medicine',
                'agency_acronym' => 'ABFM',
                'website' => 'https://www.theabfm.org/',
            ],
            [
                'specialty_type' => 'Primary Care',
                'certification_agency' => 'American Board of Internal Medicine',
                'agency_acronym' => 'ABIM',
                'website' => 'https://www.abim.org/',
            ],
            [
                'specialty_type' => 'Opthamolgy',
                'certification_agency' => 'American Board of Optholmology',
                'agency_acronym' => 'ABOP',
                'website' => 'https://abop.org/',
            ],
            [
                'specialty_type' => 'Pain Management',
                'certification_agency' => 'American Board of Preventive Medicine',
                'agency_acronym' => 'ABPM',
                'website' => 'https://www.abpm.org/',
            ],
            [
                'specialty_type' => 'Pain Management',
                'certification_agency' => 'American Board of Interventional Pain Physicians',
                'agency_acronym' => 'ABIPP',
                'website' => 'https://abipp.org/',
            ],
            [
                'specialty_type' => 'Pain Management',
                'certification_agency' => 'American Board of Physical Medicine and Rehabilitation',
                'agency_acronym' => 'ABPMR',
                'website' => 'https://www.abpmr.org/Subspecialties/Pain',
            ],
            [
                'specialty_type' => 'Pathology',
                'certification_agency' => 'American Board of Pathology',
                'agency_acronym' => 'ABPATH',
                'website' => 'https://www.abpath.org/',
            ],
            [
                'specialty_type' => 'Gastroenterology',
                'certification_agency' => 'American Board of Internal Medicine',
                'agency_acronym' => 'ABIM',
                'website' => 'https://www.abim.org/',
            ],
            [
                'specialty_type' => 'Cardiology',
                'certification_agency' => 'American Board of Medical Specialties',
                'agency_acronym' => 'ABMS',
                'website' => 'https://www.abms.org/',
            ],
            [
                'specialty_type' => 'Audiology',
                'certification_agency' => 'American Board of Audiology',
                'agency_acronym' => 'ABA',
                'website' => 'https://www.audiology.org/american-board-of-audiology/aba-certification/',
            ],
            [
                'specialty_type' => 'Dermatology',
                'certification_agency' => 'American Board of Dermatology',
                'agency_acronym' => 'ABD',
                'website' => 'https://www.abderm.org/',
            ],
            [
                'specialty_type' => 'ENT',
                'certification_agency' => 'American Board of Otolaryngology',
                'agency_acronym' => 'ABO',
                'website' => 'https://www.abderm.org/',
            ],
            [
                'specialty_type' => 'Endocrinology',
                'certification_agency' => 'American Osteopathic Board of Internal Medicine',
                'agency_acronym' => 'AOA',
                'website' => 'https://certification.osteopathic.org/internal-medicine/',
            ],
            [
                'specialty_type' => 'Pulmonary',
                'certification_agency' => 'American Board of Internal Medicine',
                'agency_acronym' => 'ABIM',
                'website' => 'https://www.abim.org/certification/exam-information/pulmonary-disease',
            ],
            [
                'specialty_type' => 'Urology',
                'certification_agency' => 'American Board of Urology',
                'agency_acronym' => 'ABU',
                'website' => 'https://www.abu.org/',
            ],
            [
                'specialty_type' => 'Neurology',
                'certification_agency' => 'American Board of Psychiatry and Neurology',
                'agency_acronym' => 'ABPN',
                'website' => 'https://www.abpn.com/',
            ],
            [
                'specialty_type' => 'Orthopedics',
                'certification_agency' => 'American Board of Orthopaedic Surgery',
                'agency_acronym' => 'ABOS',
                'website' => 'https://www.abms.org/board/american-board-of-orthopaedic-surgery/',
            ],
            [
                'specialty_type' => 'ALL SPECIALITES',
                'certification_agency' => 'American Board of Medical Specialties',
                'agency_acronym' => 'ABMS',
                'website' => 'https://www.abms.org/',
            ],
            [
                'specialty_type' => 'ALL SPECIALITES',
                'certification_agency' => 'American Board of Physician Specialties',
                'agency_acronym' => 'ABPS',
                'website' => 'https://www.abpsus.org/',
            ],
        ];

        PhysicianSpeciality::insert($types);
    }
}
