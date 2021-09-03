@extends('admin.public.header')
@section('title','编辑服务商信息')
@section('listcontent')
    <div class="layui-form layuimini-form">
        <input type="hidden" name="id" value="{{$info->id}}" />

        <div class="layui-form-item">
            <label class="layui-form-label required">标识</label>
            <div class="layui-input-block">
                <input type="text" lay-verify="required" lay-reqtext="标识不能为空" placeholder="请输入标识" value="{{$info->code ?? ''}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label required">服务商</label>
            <div class="layui-input-block">
                <input type="text" lay-verify="required" lay-reqtext="服务商不能为空" placeholder="请输入服务商" value="{{$info->remark ?? ''}}" class="layui-input" disabled />
            </div>
        </div>

        <div class="hr-line"></div>

        <div class="layui-form-item">
            <label class="layui-form-label">配置参数</label>
        </div>
        @include("smsview::admin.public.$info->param_view", ['data' => $info->params])

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
                var id=data.field.id;
                $("#saveBtn").addClass("layui-btn-disabled");
                $("#saveBtn").attr('disabled', 'disabled');
                $.ajax({
                    url:'/admin/sms_gateway/edit',
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