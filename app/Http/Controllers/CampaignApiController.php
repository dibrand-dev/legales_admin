<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Person;
use App\Models\SignedContract;
use Validator;
use Image;
use Illuminate\Support\Facades\Storage;

class CampaignApiController extends Controller {
    
    private function _getCampaigns(){
        return Campaign::with('brand')
        ->get()
        ->makeHidden(['contract_id', 'brand_id']);
    }
    
    public function campaigns(){
        $campaigns = $this->_getCampaigns();
        $campaignsArray = [];
        foreach ($campaigns as $campaign){
            
            $newCampaign = new \stdClass();
            $newCampaign->id = $campaign->id;
            $newCampaign->name = $campaign->name;
            $newCampaign->start_date = $campaign->start_date;
            $newCampaign->end_date = $campaign->end_date;
            $newCampaign->code = $campaign->code;
            $newCampaign->brand_id = $campaign->brand_id;
            $newCampaign->created_at = $campaign->created_at;
            $newCampaign->updated_at = $campaign->updated_at;
            $newCampaign->contractText = $campaign->contractText;
            $newCampaign->brand = new \stdClass();
            $newCampaign->brand = new \stdClass();
        

            $newCampaign->brand->id = $campaign->brand->id;
            $newCampaign->brand->name = $campaign->brand->name;

            //Aca Creamos el base64
            $path = $campaign->brand->image_path;
            $path = Storage::disk('local')->url($path);
            $path = ltrim($path, '/'); 
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            //dd($base64);
            $newCampaign->brand->logo = $base64;
            $newCampaign->brand->created_at = $campaign->brand->created_at;
            $newCampaign->brand->updated_at = $campaign->brand->updated_at;
            $newCampaign->brand->image_path = $campaign->brand->image_path;
            array_push($campaignsArray, $newCampaign);
        }
        return response()->json($campaignsArray);
    } 
    public function checksum(){
        return response()->json(md5($this->_getCampaigns()));
    }

    public function sign(Request $request){
        $data = [
            'fullname'=>$request->json('fullname'),
            'dni'=>$request->json('dni'),
            'body'=>$request->json('body'),
            'email'=>$request->json('email'),
            'picture'=>$request->json('picture'),
            'campaign'=>$request->json('campaign'),
            'signature'=>$request->json('signature')
        ];

        $validator = Validator::make($data, [
            'fullname' => 'required',
            'dni' => 'required',
            'email' => 'required|email',
            'picture' => 'required',
            'campaign' => 'required',
            'signature' => 'required',
        ]);
 
        $validated = $validator->validated();
    
        if (!$validated) {
            // get all errors 
            $errors = $validator->errors()->all();
            return response()->json([
                "success" => false,
                "message" => "Validation Error",
                "title" => $errors
            ]);
        }


            //check if person exists and create it
            $person_found = Person::where('dni', $request->json('dni'))->first();
            if($person_found){
                $person_id = $person_found->id;
            }else{
                $newPerson = new Person;
                $newPerson->fullname = $request->json('fullname');
                $newPerson->dni = $request->json('dni');
                $newPerson->email = $request->json('email');


                $image = base64_decode($request->json('picture'));
                $path = public_path().'img/avatars/' . $request->json('dni') . microtime() . ".png";
                Storage::disk('public')->put($path, $image);
                $newPerson->picture = $path;

                if($newPerson->save()){
                    $person_id = $newPerson->id;
                }
            }

            //check if campaign exists and if is in a valid date
            $campaign_found = Campaign::where('id', $request->json('campaign'))
            ->whereDate('start_date', '<=', date("Y-m-d"))
            ->whereDate('end_date', '>=', date("Y-m-d"))
            ->first();
            if(!$campaign_found){
                return response()->json([
                    "success" => false,
                    "message" => "Campaign not found or out of range of valid dates"
                ]);
            }

            //save signature to db
            $newSignature = new SignedContract;
            $newSignature->person_id = $person_id;
            $newSignature->campaign_id = $campaign_found->id;
            $newSignature->signature = $request->json('signature');
            $newSignature->signed_date = date("Y-m-d");
            if(!$newSignature->save()){
                return response()->json([
                    "success" => false,
                    "message" => "Error saving signature"
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Contract has been signed"
            ]);
        
    }
}