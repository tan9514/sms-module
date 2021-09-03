@extends('admin.public.header')
@section('title',$title)
@section('listcontent')
    <div class="layui-form layuimini-form">
        @if(isset($info->id))
        <input type="hidden" name="id" value="{{$info->id}}" />
        @endif

        <div class="layui-form-item">
            <label class="layui-form-label required">服务商</label>
            <div class="layui-input-block">
                <select name="gateway_id" lay-verify="required" lay-reqtext="请选择服务商">
                    <option value="">请选择服务商</option>
                    @foreach($gatewayList as $gateway)
                    <option value="{{$gateway->id}}" @if(isset($info->gateway_id) && $info->gateway_id == $gateway->id) selected @endif>{{$gateway->remark}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">模板名称</label>
            <div class="layui-input-block">
                <input type="text" name="temp_name" lay-verify="required" lay-reqtext="模板名称不能为空" placeholder="请输入模板名称" value="{{$info->temp_name ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">模板CODE</label>
            <div class="layui-input-block">
                <input type="text" name="temp_code" lay-verify="required" lay-reqtext="模板CODE不能为空" placeholder="请输入模板CODE" value="{{$info->temp_code ?? ''}}" class="layui-input" />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">模板内容</label>
            <div class="layui-input-block">
                <textarea name="content" lay-verify="required" lay-reqtext="模板内容不能为空" placeholder="请输入模板内容" class="layui-textarea">{{$info->content ?? ''}}</textarea>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">已使用的变量</label>
            <div class="layui-input-block">
                @foreach($variableList as $variable)
                <input type="checkbox" name="variable[]" value="{{$variable->key}}" title="{{$variable->remark}} : {{$variable->key}}" @if(isset($info->variable) && is_array($info->variable) && in_array($variable->key, $info->variable)) checked @endif />
                @endforeach
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-block">
                <input type="radio" name="status" value="1" title="等待审核" @if(!isset($info->status) || $info->status == 1) checked @endif/>
                <input type="radio" name="status" value="2" title="审核成功" @if(isset($info->status) && $info->status == 2) checked @endif />
                <input type="radio" name="status" value="3" title="审核失败" @if(isset($info->status) && $info->status == 3) checked @endif />
                <div style="font-size: 10px; color: red;">PS: 选择审核成功则表示当前模板为可用的模板，并且默认当前模板的所属服务商已经审核通过该模板了。</div>
            </div>
        </div>

        <div class="hr-line"></div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" id="saveBtn" lay-submit lay-filter="saveBtn">保存</button>
            </div>
        </div>

    </div>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['iconPickerFa', 'form', 'layer'], function () {
            var iconPickerFa = layui.iconPickerFa,
                form = layui.form,
                layer = layui.layer,
                $ = layui.$;

            //监听提交
            form.on('submit(saveBtn)', function(data){
                $("#saveBtn").addClass("layui-btn-disabled");
                $("#saveBtn").attr('disabled', 'disabled');
                $.ajax({
                    url:'/admin/sms_temp/edit',
                    type:'post',
                    data:data.field,
                    dataType:'JSON',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(res){
                        if(res.code==0){
                            var index = parent.layer.getFrameIndex(window.name);
                            layer.msg(res.message,{icon: 1},function (){
                                parent.layer.close(index)
                            });
                        }else{
                            layer.msg(res.message,{icon: 2});
                            $("#saveBtn").removeClass("layui-btn-disabled");
                            $("#saveBtn").removeAttr('disabled');
                        }
                    },
                    error:function (data) {
                        layer.msg(res.message,{icon: 2});
                        $("#saveBtn").removeClass("layui-btn-disabled");
                        $("#saveBtn").removeAttr('disabled');
                    }
                });
            });
        });
    </script>
@endsection