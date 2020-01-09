<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\ScoreCard;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of ScoreProject
 *
 * @author Sucre.xu
 */
class ScoreProject extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable=['name', 'description', 'data_to_score', 'score_save_to','audit_save_to','data_fillable',
        'audit_fillable','score_fillable','data_list_columns','data_to_score_columns','date_field','contract_no_field'];
    public function items(){
        return $this->hasMany('App\ScoreCard\ScoreItem','project_id')->orderBy('order');
    }
}