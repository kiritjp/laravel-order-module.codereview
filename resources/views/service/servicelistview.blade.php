@extends('adminlte::page')

@section('title', 'Service List')

@section('content_header')
    <h1>Service List</h1><a href="{{ url(config('adminlte.create_service_url', 'admin/service/add')) }}" class="btn btn-info pull-right add-btn">Add Service</a>
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
	@if (session('error-service'))
        <div class="alert alert-error" id="error-alert">
        	<button type="button" class="close" data-dismiss="alert">x</button>
            {{ session('error-service') }}
        </div>
    @endif	
	@if (session('success-service'))
        <div class="alert alert-success" id="success-alert">
        	<button type="button" class="close" data-dismiss="alert">x</button>
            {{ session('success-service') }}
        </div>
    @endif
		<div class="box"> 
			<div class="box-header">
              <h3 class="box-title">Note 
                <small> : Drag & Drop functionality for manage order. Do not use this functionality when apply filter.</small>
              </h3>
            </div>
			<div class="box-body table-responsive">
				<table id="serviceList" class="table table-bordered table-striped dataTable" cellspacing="0" width="100%" role="grid" style="width: 100%;">
				    <thead>
				        <tr>
				            <th>Order</th>
				            <th>Name</th>
				            <th>Price</th>
				            <th>Category</th>
				            <th>Description</th>
				            {{--  <th>Agent</th>  --}}
				            <th>Action</th>
				        </tr>
				    </thead>
				    <thead>
				    	<tr>
				            <th></th>
				            <th></th>
				            <th>
				            	<div class="custom-select">
				    				<select placeholder="Search Category" class="search-category-select form-control" style="padding: 5px 5px;width: 100%">
				    					<option value="">Select Category</option>
				    					@foreach($categoryData as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
				            </th>
				            <th></th>
				            <th></th>
				        </tr>
				    </thead>
				    <tbody id="serviceListContents">
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop

@section('css')
	<style type="text/css">
		.add-btn{
			position: absolute;
	    	float: right;
	    	right: 15px;
	    	top: 10px;
		}
	</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.11.2/css/alertify.min.css">
@stop

@section('js')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.11.2/alertify.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
        	setTimeout(function(){
        		$("#success-alert").slideUp(500);
			},5000);
            var serviceListTable = $('#serviceList').DataTable({
            	language: {
					processing: "<img src='{{asset('assets/backend/images/loader.gif')}}' height='100' width='100' alt='Loading...'>",
				},
	            processing: true,
	            serverSide: true,
	            ajax: {
	            	"url":"{{route('service.serviceList')}}",
	            	"dataType":"json",
					"type":"POST",
					"data": function(data) {
						data.category_id = $('.search-category-select').val();
						data._token = "{{ csrf_token() }}";
					}

	            },
	            columnDefs: [
					{ "width": "8%", "targets": 0 },
					{ "width": "12%", "targets": 1 },
					{ "width": "20%", "targets": 2 },
					{ "width": "20%", "targets": 3 },
					{ "width": "30%", "targets": 4 },
					{ "width": "10%", "targets": 5 }
			    ],
	            columns: [
	            	{ data: 'service_order' },
			        { data: 'name' },
			        { data: 'price' },
			        { data: 'categoryname'},
			        { data: 'description',"searchable":false, "orderable":false},
			        {{--  { data: 'agentname',"searchable":false, "orderable":false},  --}}
			        { data: 'action' ,"searchable":false, "orderable":false},
			    ]
	        });

	        $('.search-category-select').on( 'change', function () {   // for select box
	            dataTable.ajax.reload();
	        });

            $( "#serviceListContents" ).sortable({
	    		items: "tr",
	      		cursor: 'move',
	      		opacity: 0.6,
	      		update: function() {
	          		var order = [];
	          		var start_point = $('#serviceList').DataTable().page.info().start;
					$('tr.row1').each(function(index,element) {
						order.push({
						  id: $(this).attr('data-id'),
						  position: start_point+index+1
						});
					});

					$.ajax({
						type: "POST", 
						dataType: "json", 
						url: "{{route('service.UpdateOrder')}}",
						data: {
						  order:order,
						  _token: '{{csrf_token()}}'
						},
						success: function(response) {
						    if (response.status == "success") {
						    	serviceListTable.draw();
						       	alertify.set('notifier','position', 'bottom-right');
								alertify.success('Service Order updated Successfully');
						    }
						}
					});
	      		}
		    });
			$(document).on("click", '.delete_service' ,function () {
				var delete_url = $(this).data('url');
				alertify.confirm("Delete Record","Are you sure want to delete this record ?",
					function(){
						$.ajax({
							url:delete_url, 
							type: "GET",
							success: function(result){
								window.location.reload();
							}
						});
					},
					function(){
						
					}
				);
			});
        });
    </script>
@stop