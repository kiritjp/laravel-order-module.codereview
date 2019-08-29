<?php

namespace Chores\Repositories;

use Chores\Service;
use Chores\Company;
use Chores\Booking;
use DB;

class ServiceRepository {

   public function getServiceAgents($serviceId){
    return Service::where('id',$serviceId)->with('serviceagents')->first();
   }

   public function getServiceByCategory($categoryId){
      return Service::select('id','category_id','name','description','name_ar','description_ar','price')
      ->where('category_id',$categoryId)
      ->orderby('service_order', 'asc')
      ->get();
   }
   public function getCategoryIconByServiceId($serviceId){
    /*Do not remove Trashed affect on schedule*/
    return Service::withTrashed()->with(['parentcategory' => function($query){
        $query->select('id',DB::raw("if(icon !='',CONCAT('".url('/')."/images/icon/',icon),'') as icon"), 'name');
      }
    ])->where('id',$serviceId)->first();
   }

  public function getServiceAgentsByState($serviceId,$stateId){
    return Service::where('id',$serviceId)->with(['serviceagents'=>function($query) use($stateId){
        $query->whereHas('company', function($q) use($stateId) {
            $q->where('state_id', $stateId);
            $q->where('status', 1);
        });
      }])->first();
  }
  public function getCompanyByServiceId($serviceId,$state_id=''){
    $companyDetails = array();
    $companyServiceQuery = Company::select(['name', 'id','state_id', 'short_description','company_alias'])
                ->whereHas('services', function($query) use ($serviceId){
                    $query->select('price','company_id','service_id');
                    $query->where('service_id','=',$serviceId);
                })->with(['services'])->where('status','=','1');
    if($state_id !=''){
      $companyServiceQuery->where('state_id',$state_id);
    }
    $companyServiceQuery->orderBy('company_order', 'asc');
    $companyServiceDetails = $companyServiceQuery->get();
    if(!empty($companyServiceDetails)){
      foreach ($companyServiceDetails as $companyServiceDetail) {
        $companyDetails_temp = array();
        $companyDetails_temp['company_id'] =  $companyServiceDetail->id;
        $companyDetails_temp['company_name'] =  $companyServiceDetail->name;
        $companyDetails_temp['company_alias'] =  $companyServiceDetail->company_alias;
        $companyDetails_temp['state_id'] =  $companyServiceDetail->state_id;
        $companyDetails_temp['short_description'] =  $companyServiceDetail->short_description;
        foreach($companyServiceDetail->services as $services){
          if( $services->service_id == $serviceId ){
            $companyDetails_temp['price'] =  $services->price;
          }
        }
        /*Get Compnay Rating By Company Id*/
        $rating = $this->getCompanyRating($companyServiceDetail->id);
        $companyDetails_temp['rating'] =  $rating;
        $companyDetails[] = $companyDetails_temp;
      }
    }
    return $companyDetails;
  }
  public function getCompanyRating($companyId){
    $rating = Booking::whereNotNull('ratting')->where('company_id', $companyId)->avg('ratting');
    if($rating){
      return $rating;
    }else{
      return "0";
    }
  }
  public function getCompanyServiceById($companyId,$serviceId){
    $companyDetails = array();
    $companyServiceDetail = Company::select(['name', 'id','state_id'])
                ->where('id','=',$companyId)
                ->whereHas('services', function($query) use ($serviceId){
                    $query->select('price','company_id','service_id');
                    $query->where('service_id','=',$serviceId);
                })->with(['services'])->first();
    if($companyServiceDetail){
      $companyDetails['company_id'] =  $companyServiceDetail->id;
      $companyDetails['company_name'] =  $companyServiceDetail->name;
      foreach($companyServiceDetail->services as $services){
        if( $services->service_id == $serviceId ){
          $companyDetails['price'] =  $services->price;
        }
      }
      /*Get Compnay Rating By Company Id*/
      $rating = $this->getCompanyRating($companyServiceDetail->id);
      $companyDetails['rating'] =  $rating;
    }
    return $companyDetails;
  }
  public function getServiceCompaniesByState($serviceId,$stateId){
    return Company::whereHas('services', function($query) use ($serviceId){
                    $query->where('service_id','=',$serviceId);
                })->where(['status'=>'1','state_id'=>$stateId])->get();
  }
}