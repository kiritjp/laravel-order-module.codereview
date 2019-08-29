@extends('adminlte::page')

@section('title', 'Add Service')

@section('content_header')
    <h1>Add Service</h1>
@stop

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
			    <form action="{{ url(config('adminlte.create_service_form_url', 'admin/service/create')) }}" method="post" enctype="multipart/form-data">
			    {!! csrf_field() !!}
			    	<div class="col-sm-12" style="padding-left: 0px;">
				    	<div class="col-sm-6">
					    	<div class="form-group has-feedback {{ $errors->has('name') ? 'has-error' : '' }}">
					    		<label>{{ trans('adminlte::adminlte.name') }}</label>
					            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="{{ trans('adminlte::adminlte.name') }}" id="service_name">
					            
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
					            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" placeholder="{{ trans('adminlte::adminlte.name_ar') }}">
					            <!-- <span class="glyphicon glyphicon-user form-control-feedback"></span> -->
					            @if ($errors->has('name_ar'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('name_ar') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
					<div class="col-sm-12" style="padding-left: 0px;">
					    <div class="col-sm-6">
					        <div class="form-group has-feedback {{ $errors->has('slug') ? 'has-error' : '' }}">
					    		<label>{{ trans('adminlte::adminlte.slug') }}</label>
					            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="{{ trans('adminlte::adminlte.slug') }}"  id="service_slug">
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
					            <input type="number" name="service_order"  class="form-control" value="{{ $service_order }}" disabled="">
					            @if ($errors->has('service_order'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('service_order') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
					<div class="col-sm-12" style="padding-left: 0px;">
				    	<div class="col-sm-6">
					        <div class="form-group has-feedback {{ $errors->has('description') ? 'has-error' : '' }}">
					        	<label>{{ trans('adminlte::adminlte.description') }}</label>
					            <textarea name="description" class="form-control" placeholder="{{ trans('adminlte::adminlte.description') }}">{{ old('description') }}</textarea> 
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
					            <textarea name="description_ar" class="form-control" placeholder="{{ trans('adminlte::adminlte.description_ar') }}">{{ old('description_ar') }}</textarea> 
					            @if ($errors->has('description_ar'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('description_ar') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
					<div class="col-sm-12" style="padding-left: 0px;">
					    <div class="col-sm-6">
					        <div class="form-group has-feedback {{ $errors->has('category_id') ? 'has-error' : '' }}">
					        	<label>{{ trans('adminlte::adminlte.category') }}</label>
					            <select name="category_id" class="form-control"
					                   placeholder="{{ trans('adminlte::adminlte.category') }}" id="category">
					            	<option value="">Select One</option>
					            	@foreach ($categories as $category)
					            		<option value="{{ $category->id }}">{{ $category->name }}</option>
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
					            		<option value="{{ $agent->id }}">{{ $agent->company->name }}</option>
					            	@endforeach
					            </select>
					            @if ($errors->has('agent'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('agent') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
					<div class="col-sm-12" style="padding-left: 0px;">
					    <div class="col-sm-6">
					        <div class="form-group has-feedback {{ $errors->has('price') ? 'has-error' : '' }}">
					        	<label>{{ trans('adminlte::adminlte.price') }}</label>
					            <input type="number" name="price" class="form-control" value="{{ old('price') }}" placeholder="{{ trans('adminlte::adminlte.price') }}" step="0.01">
					            <!-- <span class="glyphicon glyphicon-user form-control-feedback"></span> -->
					            @if ($errors->has('price'))
					                <span class="help-block">
					                    <strong>{{ $errors->first('price') }}</strong>
					                </span>
					            @endif
					        </div>
					    </div>
					</div>
				    <div class="col-sm-12">
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
    <script type="text/javascript">
    	function string_to_slug (str) {
		    str = str.replace(/^\s+|\s+$/g, ''); // trim
		    str = str.toLowerCase();
		  
		    // remove accents, swap ñ for n, etc
		    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
		    var to   = "aaaaeeeeiiiioooouuuunc------";
		    for (var i=0, l=from.length ; i<l ; i++) {
		        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		    }

		    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		        .replace(/\s+/g, '-') // collapse whitespace and replace by -
		        .replace(/-+/g, '-'); // collapse dashes

		    return str;
		}
    	$(document).ready(function () {
    		$('#category').select2();
    		$('#agent').select2();
    		$(document).on("change", '#service_name' ,function () {
    	 		var string = $(this).val();
    	 		var slug;
    	 		slug = string_to_slug(string);
    	 		$('#service_slug').val(slug);
    	 	});
    	});
    </script>
@stop