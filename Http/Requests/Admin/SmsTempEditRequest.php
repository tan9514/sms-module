<?php

namespace Modules\Sms\Http\Requests\Admin;

use Modules\Sms\Http\Requests\BaseRequest;

class SmsTempEditRequest extends BaseRequest
{
    /**
     * 判断用户是否有请求权限
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * 获取规则
     * @return string[]
     */
    public function newRules()
    {
        return [
            'id' => 'nullable|integer|min:1',
            'gateway_id'  => 'required|integer|min:1',
            'temp_name' => 'required|string|max:100',
            'temp_code' => 'required|string|max:50',
            'content' => 'required|string',
            'variable' => 'nullable|array',
            'status' => 'required|integer|min:1|max:3',
        ];
    }

    /**
     * 获取自定义验证规则的错误消息
     * @return array
     */
    public function messages()
    {
        return [
//            'phone.regex' => "请输入正确的 :attribute",
        ];
    }

    /**
     * 获取自定义参数别名
     * @return string[]
     */
    public function attributes()
    {
        return [
            'gateway_id' => "服务商",
            'temp_name' => "模板名称",
            'temp_code' => "模板CODE",
            'content' => "模板内容",
            'variable' => "已使用的变量",
            'status' => "状态",
        ];
    }

    /**
     * 验证规则
     */
    public function check()
    {
        $validator = \Validator::make($this->all(), $this->newRules(), $this->messages(), $this->attributes());
        $error = $validator->errors()->first();
        if($error){
            return $this->resultErrorAjax($error);
        }
    }
}
