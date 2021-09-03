@foreach($info->params as $k=>$v)
<div class="layui-form-item">
    <label class="layui-form-label">{{$k}}</label>
    <div class="layui-input-block">
        @if($v === true || $v === false)
            <input type="radio" name="params[{{$k}}]" value="true" title="true" @if($v === true) checked="" @endif>
            <input type="radio" name="params[{{$k}}]" value="false" title="false" @if($v === false) checked="" @endif>
        @else
            <input type="text" name="params[{{$k}}]" placeholder="请输入{{$k}}" value="{{$v ?? ''}}" class="layui-input" />
        @endif

    </div>
</div>
@endforeach