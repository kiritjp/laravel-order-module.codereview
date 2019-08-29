<?php

namespace Chores\Http\Controllers\Admin;

use Chores\Http\Controllers\Controller;
use Chores\Service;
use Chores\Category;
use Chores\ServiceAgent;
use Chores\Agent;
use Illuminate\Http\Request;
use Auth;
use yajra\Datatables\Datatables;
use Chores\Repositories\ServiceRepository;

class ServiceController extends Controller
{
    protected $redirectTo = '/backend/login';
    protected $serviceRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ServiceRepository $serviceRepo)
    {
        $this->middleware('admin');
        $this->serviceRepo = $serviceRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoryData = Category::all();
        return view('service.servicelistview')->with(compact('categoryData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $agents = Agent::with('role','company')->whereHas('role',function($query){
            $query->where('name', '=', 'Agent');
        })->get();
        $service_order = Service::max('service_order') + 1;
        return view('service.serviceadd')->with(compact('categories','agents','service_order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'category_id' => 'required|numeric|exists:categories,id',
            'slug' => 'required|string|max:100|unique:services',
            'agent' => 'required',
            'description' => 'required',
            'description_ar' => 'required',
            'price' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        ],['agent.required'=>'The company agent field is required.']);
        $serviceData = Service::create([
            'name'          =>$request->name,
            'slug'          =>$request->slug,
            'name_ar'          =>$request->name_ar,
            'description'   => $request->description,
            'description_ar'   => $request->description_ar,
            'category_id'   => $request->category_id,
            'price'         => sprintf("%.2f",$request->price),
            'created_by'    => Auth::guard()->user()->id,
            'service_order'      => ( Service::max('service_order') + 1 )
        ]);
          foreach($request->agent as $agent){
                ServiceAgent::create([
                    'service_id'       => $serviceData->id,
                    'user_id'          => $agent,
                ]);
            }
       
        return redirect('admin/service')->with('success-service', 'Service Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Chores\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Chores\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service,$id)
    {
        $id = base64_decode($id);
        $serviceData = Service::with('category','agents.serviceAgent')->find($id);
       
        $categories = Category::all();
        $agents = Agent::with('role','company')->whereHas('role',function($query){
            $query->where('name', '=', 'Agent');
        })->get();
        return view('service.serviceedit')->with(compact('categories','serviceData','agents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Chores\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service, $id)
    {
        $id = base64_decode($id);
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'category_id' => 'required|numeric|exists:categories,id',
            'slug' => 'required|string|max:100|unique:services,slug,'.$id.',id,category_id,'.$request->category_id,
            'agent' => 'required',
            'description' => 'required',
            'description_ar' => 'required',
            'price' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        ]);
        $service = Service::find($id);

        $service->name = $request->name;
        $service->name_ar = $request->name_ar;
        $service->category_id = $request->category_id;
        $service->description = $request->description;
        $service->description_ar = $request->description_ar;
        $service->price = sprintf("%.2f",$request->price);
        $service->updated_by = Auth::guard()->user()->id;
        $service->save();

        $service->serviceagents()->sync($request->agent);

        return redirect('admin/service')->with('success-service', 'Service updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Chores\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service, $id)
    {
        $id = base64_decode($id);

        $category = Service::find($id);
        $category->delete();
        return redirect('admin/service')->with('error-service', 'Service Deleted!');
    }

    /**
     * Display the Category resource in datatable.
     *
     * @param  \Chores\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function serviceList(Request $request)
    {
        if($request['category_id']){
            $category_id = $request['category_id'];
            $services = Service::select(['name', 'price', 'id', 'service_order', 'category_id','description'])->with(['parentcategory'=>function($query) use($category_id){
                $query->select('name','categories.id');
                }])->where('category_id','=',$category_id)->get();
        }else{
            $services = Service::select(['name', 'price', 'id', 'service_order', 'category_id','description'])->with(['parentcategory'=>function($query){
                $query->select('name','categories.id');
                }])->get();
        }

        return Datatables::of($services)->setRowAttr([
                'data-id' => function($row) {
                    return $row->id;
                },
            ])->setRowClass('row1')->editColumn('categoryname', function ($row){
            if($row->parentcategory->name){
                return $row->parentcategory->name ;
            }
            else{
                return '';
            }
             
        })->addColumn('action', function ($row) {
            $edit_route = route('service.Editview',base64_encode($row->id));
            $delete_route = route('service.Delete',base64_encode($row->id));
                return '
                    <a href="'.$edit_route.'" class="btn btn-warning btn-xs">Edit</a>
                     <a href="#" data-url="'.$delete_route.'" class="btn btn-danger btn-xs delete_service">Delete</a>
                ';
        })->make(true);
    }

    public function getservicebycategory(Request $request){
        $services = Service::select(['id','name as text'])->where('category_id', '=', $request->category_id)->get();
        return json_encode(array('service'=>$services));
    }
    
    public function getcompanybyservice(Request $request){
        $companyDetails = $this->serviceRepo->getCompanyByServiceId($request->service_id);       
        return json_encode(array('company'=>$companyDetails));

    }

    /*This is for existing services which don't have it's own slug.*/
    public function updateServiceSlug(){
        $services = Service::all();
        foreach ($services as $service) {
            $slug = strtolower(str_slug($service->name));
            $updateService = Service::find($service->id);
            $updateService->slug = $slug;
            $updateService->save();
        }

    }

    public function updateOrderService(Request $request){
        foreach ($request->order as $order) {
            $id = $order['id'];
            $service = Service::find($id);
            $service->update(['service_order' => $order['position']]);
        }        
        return json_encode(array('status'=>'success'));
    }
}
