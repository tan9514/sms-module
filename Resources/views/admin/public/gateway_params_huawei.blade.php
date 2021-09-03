@foreach($info->params as $k=>$v)
@if($k !== 'from')
<div class="layui-form-item">
    <label class="layui-form-label">{{$k}}</label>
    <div class="layui-input-block">
        <input type="text" name="params[{{$k}}]" placeholder="请输入{{$k}}" value="{{$v ?? ''}}" class="layui-input" />
    </div>
</div>
@else
<div class="layui-form-item">
    <label class="layui-form-label">签名</label>
    <div class="layui-input-block">
        <div id="huawei-param-form">
            @foreach($v as $formk => $formv)
            @if($formk == 'default')
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">默认签名</label>
                    <div class="layui-input-inline">
                        <input type="text" placeholder="请输入签名标识" name="from[code][]" autocomplete="off" value="{{$formk}}" class="layui-input" disabled />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">签名内容</label>
                    <div class="layui-input-inline">
                        <input type="text" name="from[value][]" placeholder="请输入签名内容" autocomplete="off" value="{{$formv}}" class="layui-input" />
                    </div>
                </div>
            </div>
            @else
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">签名标识</label>
                    <div class="layui-input-inline">
                        <input type="text" name="from[code][]" placeholder="请输入签名标识" autocomplete="off" value="{{$formk}}" class="layui-input" />
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">签名内容</label>
                    <div class="layui-input-inline">
                        <input type="text" name="from[value][]" placeholder="请输入签名内容" autocomplete="off" value="{{$formv}}" class="layui-input" />
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <a href="javascript:void(0)" class="layui-btn layui-btn-danger layui-btn-sm delform">删除</a>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            <div class="layui-form-item">
                <a href="javascript:void(0)" class="layui-btn" id="addform">添加通道</a>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

<script src="https://code.jquery.com/jquery-1.8.0.min.js"></script>
<script>
    $(function (){
        // 处理添加通道
        var addformBtn = $("#addform");
        addformBtn.click(function (){
            var formdiv = '<div class="layui-form-item">' +
                    '<div class="layui-inline">' +
                        '<label class="layui-form-label">签名标识</label>' +
                        '<div class="layui-input-inline">' +
                            '<input type="text" name="from[code][]" placeholder="请输入签名标识" autocomplete="off" class="layui-input">' +
                        '</div>' +
                    '</div>' +
                    '<div class="layui-inline">' +
                        '<label class="layui-form-label">签名内容</label>' +
                        '<div class="layui-input-inline">' +
                            '<input type="text" name="from[value][]" placeholder="请输入签名内容" autocomplete="off" class="layui-input">' +
                        '</div>' +
                    '</div>' +
                    '<div class="layui-inline">' +
                        '<div class="layui-input-inline">' +
                            '<a href="javascript:void(0)" class="layui-btn layui-btn-danger layui-btn-sm delform">删除</a>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            addformBtn.parent().before(formdiv);
        })

        // 处理删除通道
        $("#huawei-param-form").on("click",".delform",function(){
            $(this).parent().parent().parent().remove();
        });
    })
</script>