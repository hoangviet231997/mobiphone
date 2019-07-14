<?php

namespace Modules\Rule\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Rule\Entities\Rule;
use Modules\Rule\Http\Requests\CreateRuleRequest;
use Modules\Rule\Http\Requests\UpdateRuleRequest;
use Yajra\DataTables\DataTables;

class RuleController extends BaseController
{

    public function store(CreateRuleRequest $request)
    {
        $dataInput = $request->all();
        $rule  =  Rule::create($dataInput);
        return $this->responseSuccess($rule,'Created rule success!');
    }


    public function show($idRule)
    {
        $rule = Rule::find($idRule);
        if (is_null($rule))
            return $this->responseBadRequest('Rule not found!');
        $rule->rule_start_time = date('H:i',strtotime($rule->rule_start_time));
        $rule->rule_end_time = date('H:i',strtotime($rule->rule_end_time));

        return $this->responseSuccess($rule,'Show rule success!');
    }
    public function showAll()
    {
        $data = Rule::all();
        return DataTables::of($data)->make(true);
    }


    public function update(UpdateRuleRequest $request, $idRule)
    {
        $rule = Rule::find($idRule);
        if (is_null($rule))
            return $this->responseBadRequest('Rule is not found!');
        $dataInput = $request->dataOnly();
        $rule->update($dataInput);
        return $this->responseSuccess($rule,'Updated rule success!');
    }


    public function destroy($idRule)
    {
        $rule = Rule::find($idRule);
        if (is_null($rule))
            return $this->responseBadRequest('Rule is not found!');
        $rule->delete();
        return $this->responseSuccess(null,'Deleted rule success!');
    }

    public function onoffCamera($idRule,$status){
        $rule = Rule::find($idRule);
        if(!$rule){
            return $this->responseBadRequest('Screen not found!');
        }
        $rule->rule_status = $status;
        $rule->update();
        return $this->responseSuccess($rule,'status screen success!');
    }
}
