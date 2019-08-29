<?php

namespace Chores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id','name','slug','description' ,'name_ar','description_ar', 'price', 'banner', 'icon','created_by','service_order',
    ];
    
    protected $hidden = array('created_by','updated_by','created_at','deleted_at','updated_at','pivot');

    public function category()
    {
        return $this->belongsTo('Chores\Category','category_id')->select('id','name');
    }

    public function parentcategory()
    {
        return $this->belongsTo('Chores\Category','category_id');
    }
    

    public function agents()
    {
        return $this->hasMany('Chores\ServiceAgent','service_id');
    }

     public function serviceagents(){
        return $this->belongsToMany('Chores\User', 'service_agents', 'service_id', 'user_id');
    }

    public function servicebanner(){
        return $this->belongsToMany('Chores\Banner', 'banner_services', 'service_id', 'banner_id');
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint) {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }

    

}
