<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LguPosition;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApplicationResource::collection(
            Application::all()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request)
    {
        $request->validated($request->all());

        // check if the applicant is an employee
        $filters = [["first_name", $request->first_name], ["last_name", $request->last_name], ["employee_status", "Active"], ["middle_name", $request->middle_name], ["suffix", $request->suffix]];

        if ($request->middle_name == "") {
            unset($filters[3]);
        }

        if ($request->suffix == "") {
            array_pop($filters);
        }

        $employeeExist = Employee::where($filters)->exists();

        if ($employeeExist) {
            $data = Employee::where($filters)->latest()->first();

            return $data;
            $employee = Employee::find($data->id);
            $employee->update([
                "division_id" => $request->division_id,
                "employee_id" => $request->employee_id,
                "first_name" => $request->first_name,
                "middle_name" => $request->middle_name,
                "last_name" => $request->last_name,
                "suffix" => $request->suffix,
                "mobile_number" => $request->mobile_number,
                "email_address" => $request->email_address,
                "lgu_position_id" => $request->lgu_position_id,
                "employment_status" => $request->employment_status,
                "employee_status" => $request->employee_status,
                "orientation_status" => "Completed"
            ]);

            $pds =  $employee->latestPersonalDataSheet;

            // personal Information
            $personalnformation = $pds->personalInformation->update(
                [
                    'birth_place' => $request->birth_place,
                    'birth_date' => $request->birth_date,
                    'age' => $request->age,
                    'sex' => $request->sex,
                    'height' => $request->height,
                    'weight' => $request->weight,
                    'citizenship' => $request->citizenship,
                    'citizenship_type' => $request->citizenship_type,
                    'country' => $request->country,
                    'blood_type' => $request->blood_type,
                    'civil_status' => $request->civil_status,
                    'tin' => $request->tin,
                    'gsis' => $request->gsis,
                    'pagibig' => $request->pagibig,
                    'philhealth' => $request->philhealth,
                    'sss' => $request->sss,
                    'residential_province' => $request->residential_province,
                    'residential_municipality' => $request->residential_municipality,
                    'residential_barangay' => $request->residential_barangay,
                    'residential_house' => $request->residential_house,
                    'residential_subdivision' => $request->residential_subdivision,
                    'residential_street' => $request->residential_street,
                    'residential_zipcode' => $request->residential_zipcode,
                    'permanent_province' => $request->permanent_province,
                    'permanent_municipality' => $request->permanent_municipality,
                    'permanent_barangay' => $request->permanent_barangay,
                    'permanent_house' => $request->permanent_house,
                    'permanent_subdivision' => $request->permanent_subdivision,
                    'permanent_street' => $request->permanent_street,
                    'permanent_zipcode' => $request->permanent_zipcode,
                    'telephone' => $request->telephone,
                    'mobile_number' => $request->mobile_number,
                    'email_address' => $request->email_address
                ]
            );

            // Family background
            $familyBackground = $pds->familyBackGround->update([
                'spouse_first_name' => $request->spouse_first_name,
                'spouse_middle_name' => $request->spouse_middle_name,
                'spouse_last_name' => $request->spouse_last_name,
                'spouse_suffix' => $request->spouse_suffix,
                'spouse_occupation' => $request->spouse_occupation,
                'spouse_employer' => $request->spouse_employer,
                'spouse_employer_address' => $request->spouse_employer_address,
                'spouse_employer_telephone' => $request->spouse_employer_telephone,
                'father_first_name' => $request->father_first_name,
                'father_middle_name' => $request->father_middle_name,
                'father_last_name' => $request->father_last_name,
                'father_suffix' => $request->father_suffix,
                'mother_first_name' => $request->mother_first_name,
                'mother_middle_name' => $request->mother_middle_name,
                'mother_last_name' => $request->mother_last_name,
                'mother_suffix' => $request->mother_suffix,
            ]);

            $familyBackground = $pds->familyBackGround;

            //restructure and  insert children
            $children = array_map(function ($item) use ($familyBackground) {
                return ["number" => $item['number'], "name" => $item['name'], "birthday" => $item['birthday'], "family_background_id" => $familyBackground->id];
            }, $request->children);

            $pds->childrenInformations()->forceDelete();
            $pds->childrenInformations()->createMany($children);



            // educational background
            $pds->educationalBackgrounds()->forceDelete();
            $pds->educationalBackgrounds()->createMany($request->schools);


            // eligibilities
            $pds->civilServiceEligibilities()->forceDelete();
            $pds->civilServiceEligibilities()->createMany(
                $request->eligibilities
            );

            // work experiences
            $pds->workExperiences()->forceDelete();
            $pds->workExperiences()->createMany(
                $request->workExperiences
            );

            // voluntary works
            $pds->voluntaryWorks()->forceDelete();
            $pds->voluntaryWorks()->createMany(
                $request->voluntaryWorks
            );

            // trainings
            $pds->trainingPrograms()->forceDelete();
            $pds->trainingPrograms()->createMany(
                $request->trainings
            );

            // specialskills
            $pds->specialSkillHobies()->forceDelete();
            $pds->specialSkillHobies()->createMany(
                $request->skills
            );

            // recognitions
            $pds->recognitions()->forceDelete();
            $pds->recognitions()->createMany(
                $request->recognitions
            );

            // membership
            $pds->membershipAssociations()->forceDelete();
            $pds->membershipAssociations()->createMany(
                $request->memberships
            );

            // references
            $pds->references()->forceDelete();
            $pds->references()->createMany(
                $request->characterReferences
            );

            // restructure and insert answers
            $answers = array_map(function ($item) use ($familyBackground) {
                return ["question_id" => $item['question_id'], "answer" => $item['answer'], "details" => $item['details']];
            }, $request->answers);

            $pds->answers()->forceDelete();
            $pds->answers()->createMany(
                $answers
            );
        } else {

            // validate if applicant is already in the database
            unset($filters[2]);
            $applicantExist = Applicant::where($filters)->exists();

            if ($applicantExist) {
                // if applicant exist update

    
                $data = Applicant::where($filters)->latest()->first();

                $applicant = Applicant::find($data->id);

                $applicant->update([
                    "first_name" => $request->first_name,
                    "middle_name" => $request->middle_name,
                    "last_name" => $request->last_name,
                    "suffix" => $request->suffix,
                    "mobile_number" => $request->mobile_number,
                    "email_address" => $request->email_address,
                ]);

                $pds =  $applicant->latestPersonalDataSheet;

                // personal Information
                $personalnformation = $pds->personalInformation->update(
                    [
                        'birth_place' => $request->birth_place,
                        'birth_date' => $request->birth_date,
                        'age' => $request->age,
                        'sex' => $request->sex,
                        'height' => $request->height,
                        'weight' => $request->weight,
                        'citizenship' => $request->citizenship,
                        'citizenship_type' => $request->citizenship_type,
                        'country' => $request->country,
                        'blood_type' => $request->blood_type,
                        'civil_status' => $request->civil_status,
                        'tin' => $request->tin,
                        'gsis' => $request->gsis,
                        'pagibig' => $request->pagibig,
                        'philhealth' => $request->philhealth,
                        'sss' => $request->sss,
                        'residential_province' => $request->residential_province,
                        'residential_municipality' => $request->residential_municipality,
                        'residential_barangay' => $request->residential_barangay,
                        'residential_house' => $request->residential_house,
                        'residential_subdivision' => $request->residential_subdivision,
                        'residential_street' => $request->residential_street,
                        'residential_zipcode' => $request->residential_zipcode,
                        'permanent_province' => $request->permanent_province,
                        'permanent_municipality' => $request->permanent_municipality,
                        'permanent_barangay' => $request->permanent_barangay,
                        'permanent_house' => $request->permanent_house,
                        'permanent_subdivision' => $request->permanent_subdivision,
                        'permanent_street' => $request->permanent_street,
                        'permanent_zipcode' => $request->permanent_zipcode,
                        'telephone' => $request->telephone,
                        'mobile_number' => $request->mobile_number,
                        'email_address' => $request->email_address
                    ]
                );

                // Family background
                $familyBackground = $pds->familyBackGround->update([
                    'spouse_first_name' => $request->spouse_first_name,
                    'spouse_middle_name' => $request->spouse_middle_name,
                    'spouse_last_name' => $request->spouse_last_name,
                    'spouse_suffix' => $request->spouse_suffix,
                    'spouse_occupation' => $request->spouse_occupation,
                    'spouse_employer' => $request->spouse_employer,
                    'spouse_employer_address' => $request->spouse_employer_address,
                    'spouse_employer_telephone' => $request->spouse_employer_telephone,
                    'father_first_name' => $request->father_first_name,
                    'father_middle_name' => $request->father_middle_name,
                    'father_last_name' => $request->father_last_name,
                    'father_suffix' => $request->father_suffix,
                    'mother_first_name' => $request->mother_first_name,
                    'mother_middle_name' => $request->mother_middle_name,
                    'mother_last_name' => $request->mother_last_name,
                    'mother_suffix' => $request->mother_suffix,
                ]);

                $familyBackground = $pds->familyBackGround;

                //restructure and  insert children
                $children = array_map(function ($item) use ($familyBackground) {
                    return ["number" => $item['number'], "name" => $item['name'], "birthday" => $item['birthday'], "family_background_id" => $familyBackground->id];
                }, $request->children);

                $pds->childrenInformations()->forceDelete();
                $pds->childrenInformations()->createMany($children);



                // educational background
                $pds->educationalBackgrounds()->forceDelete();
                $pds->educationalBackgrounds()->createMany($request->schools);


                // eligibilities
                $pds->civilServiceEligibilities()->forceDelete();
                $pds->civilServiceEligibilities()->createMany(
                    $request->eligibilities
                );

                // work experiences
                $pds->workExperiences()->forceDelete();
                $pds->workExperiences()->createMany(
                    $request->workExperiences
                );

                // voluntary works
                $pds->voluntaryWorks()->forceDelete();
                $pds->voluntaryWorks()->createMany(
                    $request->voluntaryWorks
                );

                // trainings
                $pds->trainingPrograms()->forceDelete();
                $pds->trainingPrograms()->createMany(
                    $request->trainings
                );

                // specialskills
                $pds->specialSkillHobies()->forceDelete();
                $pds->specialSkillHobies()->createMany(
                    $request->skills
                );

                // recognitions
                $pds->recognitions()->forceDelete();
                $pds->recognitions()->createMany(
                    $request->recognitions
                );

                // membership
                $pds->membershipAssociations()->forceDelete();
                $pds->membershipAssociations()->createMany(
                    $request->memberships
                );

                // references
                $pds->references()->forceDelete();
                $pds->references()->createMany(
                    $request->characterReferences
                );

                // restructure and insert answers
                $answers = array_map(function ($item) use ($familyBackground) {
                    return ["question_id" => $item['question_id'], "answer" => $item['answer'], "details" => $item['details']];
                }, $request->answers);

                $pds->answers()->forceDelete();
                $pds->answers()->createMany(
                    $answers
                );
            } else {

                // create applicant

                $applicant = Applicant::create([
                    "first_name" => $request->first_name,
                    "middle_name" => $request->middle_name,
                    "last_name" => $request->last_name,
                    "suffix" => $request->suffix,
                    "mobile_number" => $request->mobile_number,
                    "email_address" => $request->email_address
                ]);


                $pds =  $applicant->personalDataSheets()->create(['pds_date' => date('Y-m-d')]);

                // personal Information
                $personalnformation = $pds->personalInformation()->create(
                    [
                        'birth_place' => $request->birth_place,
                        'birth_date' => $request->birth_date,
                        'age' => $request->age,
                        'sex' => $request->sex,
                        'height' => $request->height,
                        'weight' => $request->weight,
                        'citizenship' => $request->citizenship,
                        'citizenship_type' => $request->citizenship_type,
                        'country' => $request->country,
                        'blood_type' => $request->blood_type,
                        'civil_status' => $request->civil_status,
                        'tin' => $request->tin,
                        'gsis' => $request->gsis,
                        'pagibig' => $request->pagibig,
                        'philhealth' => $request->philhealth,
                        'sss' => $request->sss,
                        'residential_province' => $request->residential_province,
                        'residential_municipality' => $request->residential_municipality,
                        'residential_barangay' => $request->residential_barangay,
                        'residential_house' => $request->residential_house,
                        'residential_subdivision' => $request->residential_subdivision,
                        'residential_street' => $request->residential_street,
                        'residential_zipcode' => $request->residential_zipcode,
                        'permanent_province' => $request->permanent_province,
                        'permanent_municipality' => $request->permanent_municipality,
                        'permanent_barangay' => $request->permanent_barangay,
                        'permanent_house' => $request->permanent_house,
                        'permanent_subdivision' => $request->permanent_subdivision,
                        'permanent_street' => $request->permanent_street,
                        'permanent_zipcode' => $request->permanent_zipcode,
                        'telephone' => $request->telephone,
                        'mobile_number' => $request->mobile_number,
                        'email_address' => $request->email_address
                    ]
                );

                // Family background
                $familyBackground = $pds->familyBackGround()->create([
                    'spouse_first_name' => $request->spouse_first_name,
                    'spouse_middle_name' => $request->spouse_middle_name,
                    'spouse_last_name' => $request->spouse_last_name,
                    'spouse_suffix' => $request->spouse_suffix,
                    'spouse_occupation' => $request->spouse_occupation,
                    'spouse_employer' => $request->spouse_employer,
                    'spouse_employer_address' => $request->spouse_employer_address,
                    'spouse_employer_telephone' => $request->spouse_employer_telephone,
                    'father_first_name' => $request->father_first_name,
                    'father_middle_name' => $request->father_middle_name,
                    'father_last_name' => $request->father_last_name,
                    'father_suffix' => $request->father_suffix,
                    'mother_first_name' => $request->mother_first_name,
                    'mother_middle_name' => $request->mother_middle_name,
                    'mother_last_name' => $request->mother_last_name,
                    'mother_suffix' => $request->mother_suffix,
                ]);


                //restructure and  insert children
                $children = array_map(function ($item) use ($familyBackground) {
                    return ["number" => $item['number'], "name" => $item['name'], "birthday" => $item['birthday'], "family_background_id" => $familyBackground->id];
                }, $request->children);

                $pds->childrenInformations()->createMany($children);

                // educational background
                $pds->educationalBackgrounds()->createMany($request->schools);


                // eligibilities
                $pds->civilServiceEligibilities()->createMany(
                    $request->eligibilities
                );

                // work experiences
                $pds->workExperiences()->createMany(
                    $request->workExperiences
                );

                // voluntary works
                $pds->voluntaryWorks()->createMany(
                    $request->voluntaryWorks
                );

                // trainings
                $pds->trainingPrograms()->createMany(
                    $request->trainings
                );

                // specialskills
                $pds->specialSkillHobies()->createMany(
                    $request->skills
                );

                // recognitions
                $pds->recognitions()->createMany(
                    $request->recognitions
                );

                // membership
                $pds->membershipAssociations()->createMany(
                    $request->memberships
                );

                // references
                $pds->references()->createMany(
                    $request->characterReferences
                );

                // restructure and insert answers
                $answers = array_map(function ($item) use ($familyBackground) {
                    return ["question_id" => $item['question_id'], "answer" => $item['answer'], "details" => $item['details']];
                }, $request->answers);

                $pds->answers()->createMany(
                    $answers
                );
            }
        }





        return $this->success('', 'Successfully Saved.', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application)
    {
        return new ApplicationResource($application);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Application $application)
    {
        $application->submission_date = $request->submission_date;
        $application->first_name = $request->first_name;
        $application->middle_name = $request->middle_name;
        $application->last_name = $request->last_name;
        $application->suffix = $request->suffix;
        $application->application_type = $request->application_type;

        $application->save();

        // return $this->success('', 'Successfully Updated', 200);
        return new ApplicationResource($application);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application)
    {
        $application->delete();
        return $this->success('', 'Successfully Deleted', 200);
    }


    public function searchPerson(Request $request)
    {
        $details = null;
        $filters = [];


        // format filters
        if ($request->suffix != "") {
            $filters = [
                ['employee_id', "like", "%" . $request->employee_id . "%"],
                ['first_name', "like", "%" . $request->first_name . "%"],
                ['middle_name', "like", "%" . $request->middle_name . "%"],
                ['last_name', "like", "%" . $request->last_name . "%"],
                ['suffix', "like", "%" . $request->suffix . "%"]
            ];
        } else {
            $filters =   [
                ['employee_id', "like", "%" . $request->employee_id . "%"],
                ['first_name', "like", "%" . $request->first_name . "%"],
                ['middle_name', "like", "%" . $request->middle_name . "%"],
                ['last_name', "like", "%" . $request->last_name . "%"]
            ];
        }

        // check middlename if has data
        if ($request->middle_name != "") {
            unset($filters[2]);
        }

        // check if employee exists
        $employee = Employee::where($filters)->latest()->first();


        if ($employee != null) {
            $details = Employee::find($employee->id);
            $pds = $details->latestPersonalDataSheet;
            $personalInformation = $pds->personalInformation;
            $familyBackground = $pds->familyBackGround;
            $children = $pds->childrenInformations;
            $schools = $pds->educationalBackgrounds;
            $eligibilities = $pds->civilServiceEligibilities;
            $workExperiences = $pds->workExperiences;
            $voluntaryWorks = $pds->voluntaryWorks;
            $trainings = $pds->trainingPrograms;
            $skills = $pds->specialSkillHobies;
            $recognitions = $pds->recognitions;
            $memberships = $pds->membershipAssociations;
            $answers = $pds->answers;
            $characterReferences = $pds->references;
            $division = Division::find($employee->division_id);
            $lguPositionData = LguPosition::find($employee->lgu_position_id);
            $lguPosition = $lguPositionData->position->title . '-' . $lguPositionData->item_number;
        } else {
            unset($filters[0]);

            $applicant = Applicant::where($filters)->latest()->first();

            if ($applicant != null) {
                $details = Applicant::find($applicant->id);
                $pds = $details->latestPersonalDataSheet;
                $personalInformation = $pds->personalInformation;
                $familyBackground = $pds->familyBackGround;
                $children = $pds->childrenInformations;
                $schools = $pds->educationalBackgrounds;
                $eligibilities = $pds->civilServiceEligibilities;
                $workExperiences = $pds->workExperiences;
                $voluntaryWorks = $pds->voluntaryWorks;
                $trainings = $pds->trainingPrograms;
                $skills = $pds->specialSkillHobies;
                $recognitions = $pds->recognitions;
                $memberships = $pds->membershipAssociations;
                $answers = $pds->answers;
                $characterReferences = $pds->references;
                $division = "";
                $lguPositionData = "";
                $lguPosition = "";
            }
        }

        if ($details != null) {
            return compact(
                'details',
                'pds',
                'division',
                'lguPosition',
                'personalInformation',
                'familyBackground',
                'children',
                'schools',
                'eligibilities',
                'workExperiences',
                'voluntaryWorks',
                'trainings',
                'skills',
                'recognitions',
                'memberships',
                'answers',
                'characterReferences'
            );
        } else {
            return [];
        }
    }
}
