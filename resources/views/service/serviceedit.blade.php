@extends('adminlte::page')

@section('title', 'Edit Service')

@section('content_header')
    <h1>Edit Service</h1>
@stop
@php $selected_agent = [];
$category_agent_id = ''; 
@endphp
@section('content')
	<div class="col-sm-12">
		<div class="box box-warning">
			<div class="box-body">
		        @if (session('error-service'))
		            <div class="alert alert-danger">
		                {{ session('error-service') }}
		            </div>
		        @endif
		        @if (session('success-service'))
		            <div class="alert alert-success">
		                {{ session('success-service') }}
		            </div>
		        @endif
		        @if(!empty($serviceData->agents))
		        	@foreach($serviceData->agents as $agent)
		        		<?php $category_agent_id = $agent->id; ?>
		        		@if(isset($agent->serviceAgent->id))
		        			<?php  $selected_agent[] = $agent->serviceAgent->id; ?>
		        		@endif
		        	@endforeach
		        @endif
			    <form action="{{ url(config('adminlte.update_service_form_url', 'admin/service/update')) }}/{{base64_encode($serviceData->id)}}" method="post" enctype="multipart/form-data">
			    {!! csrf_field() !!}
			    	<div class="col-sm-6">
				    	<div class="form-group has-feedback {{ $errors->has('name') ? 'has-error' : '' }}">
				    		<label>{{ trans('adminlte::adminlte.name') }}</label>
				            <input type="text" name="name" class="form-control" value="{{ $serviceData->name }}" placeholder="{{ trans('adminlte::adminlte.name') }}">
				            @if ($errors->has('name'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('name') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-6">
				    	<div class="form-group has-feedback {{ $errors->has('name_ar') ? 'has-error' : '' }}">
				    		<label>{{ trans('adminlte::adminlte.name_ar') }}</label>
				            <input type="text" name="name_ar" class="form-control" value="{{ $serviceData->name_ar }}" placeholder="{{ trans('adminlte::adminlte.name_ar') }}">
				            @if ($errors->has('name_ar'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('name_ar') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-12" style="padding-left: 0px;">
					    <div class="col-sm-6">
					    	<div class="form-group has-feedback {{ $errors->has('slug') ? 'has-error' : '' }}">
					    		<label>{{ trans('adminlte::adminlte.slug') }}</label>
					            <input type="text" name="slug" class="form-control" value="{{ $serviceData->slug }}" placeholder="{{ trans('adminlte::adminlte.slug') }}">
					            @if ($errors->has('slug'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('slug') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					    <div class="col-sm-6">
					    	<div class="form-group has-feedback {{ $errors->has('service_order') ? 'has-error' : '' }}">
					    		<label>{{ trans('adminlte::adminlte.service_order') }}</label>
					            <input type="number" name="service_order"  class="form-control" value="{{ $serviceData->service_order }}" disabled="">
					            @if ($errors->has('service_order'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('service_order') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
				    <div class="col-sm-6">
				        <div class="form-group has-feedback {{ $errors->has('description') ? 'has-error' : '' }}">
				        	<label>{{ trans('adminlte::adminlte.description') }}</label>
				            <textarea name="description" class="form-control" placeholder="{{ trans('adminlte::adminlte.description') }}">{{ $serviceData->description }}</textarea> 
				            @if ($errors->has('description'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('description') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-6">
				        <div class="form-group has-feedback {{ $errors->has('description_ar') ? 'has-error' : '' }}">
				        	<label>{{ trans('adminlte::adminlte.description_ar') }}</label>
				            <textarea name="description_ar" class="form-control" placeholder="{{ trans('adminlte::adminlte.description_ar') }}">{{ $serviceData->description_ar }}</textarea> 
				            @if ($errors->has('description_ar'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('description_ar') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-6">
				        <div class="form-group has-feedback {{ $errors->has('category_id') ? 'has-error' : '' }}">
				        	<label>{{ trans('adminlte::adminlte.category') }}</label>
				            <select name="category_id" class="form-control"
				                   placeholder="{{ trans('adminlte::adminlte.category') }}" id="category">
				            	<option value="">Select One</option>
				            	@foreach ($categories as $category)
				            		@if($serviceData->category->id == $category->id)
				            			<option value="{{ $category->id }}" selected>{{ $category->name }}</option>
				            		@else
				            			<option value="{{ $category->id }}" >{{ $category->name }}</option>
				            		@endif
				            	@endforeach
				            </select>
				            @if ($errors->has('category_id'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('category_id') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-6">
				        <div class="form-group has-feedback {{ $errors->has('agent') ? 'has-error' : '' }}">
				        	<label>{{ trans('adminlte::adminlte.company') }}</label>
				            <select name="agent[]" class="form-control select2" multiple
				                   placeholder="{{ trans('adminlte::adminlte.agent') }}" id="agent">
				            	<option value="">Select One</option>
								@foreach ($agents as $agent)
								
								
				            		@if(in_array($agent->id,$selected_agent))
				            			<option value="{{ $agent->id }}" selected>{{ $agent->company->name }}</option>
				            		@else
				            			<option value="{{ $agent->id }}" >{{ $agent->company->name }}</option>
				            		@endif
				            	@endforeach
				            </select>
				            @if ($errors->has('agent'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('agent') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-6">
				        <div class="form-group has-feedback {{ $errors->has('price') ? 'has-error' : '' }}">
				        	<label>{{ trans('adminlte::adminlte.price') }}</label>
				            <input type="number" name="price" class="form-control" value="{{ $serviceData->price }}" placeholder="{{ trans('adminlte::adminlte.price') }}" step="0.01">
				            @if ($errors->has('price'))
				                <span class="help-block">
				                    <strong>{{ $errors->first('price') }}</strong>
				                </span>
				            @endif
				        </div>
				    </div>
				    <div class="col-sm-12">
			            <input type="hidden" name="category_agent_id" value="{{$category_agent_id}}">
				   		<button type="submit" class="btn btn-primary">{{trans('adminlte::adminlte.save') }}</button>
				   	</div>
			    </form>
			</div>
		</div>
	</div>
@stop

@section('css')
@stop

@section('js')
    <script>
    	$(document).ready(function () {
    		$('#category').select2();
    		$('#agent').select2();
    	});
    </script>
@stop