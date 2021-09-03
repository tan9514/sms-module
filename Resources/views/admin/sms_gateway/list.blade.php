@extends('admin.public.header')
@section('title','服务商列表')

@section('listsearch')
    <fieldset class="table-search-fieldset" style="display:none">
        <legend>搜索信息</legend>
        <div style="margin: 10px 10px 10px 10px">
            <form class="layui-form layui-form-pane form-search" action="" id="searchFrom">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="is_default">
                                <option value="">全部</option>
                                <option value="1">启用</option>
                                <option value="0">禁用</option>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-sm layui-btn-normal"  lay-submit lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
@endsection

@section('listcontent')
    <table class="layui-hide" id="tableList" lay-filter="tableList"></table>
    <!-- 表头左侧按钮 -->
    <script type="text/html" id="toolbarColumn">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layuimini-btn-primary" onclick="window.location.reload();" ><i class="layui-icon layui-icon-refresh-3"></i></button>
        </div>
    </script>
    <!-- 操作按钮 -->
    <script type="text/html" id="barOperate">
        <a class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
    </script>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['form','table','laydate'], function(){
            var table = layui.table, $=layui.jquery, form = layui.form, laydate = layui.laydate;
            //日期
            laydate.render({
                elem: '#begin_time'
                ,theme: '#393D49'
                ,festival: true //显示节日
                ,istime: false
                ,choose: function(datas){ //选择日期完毕的回调
                    compare_time($('#begin_time').val(),$('#end_time').val());
                }
            });
            laydate.render({
                elem: '#end_time'
                ,theme: '#393D49'
                ,festival: true //显示节日
                ,istime: false
                ,choose: function(datas){ //选择日期完毕的回调
                    compare_time($('#begin_time').val(),$('#end_time').val());
                }
            });
            // 渲染表格
            table.render({
                elem: '#tableList',
                url:'/admin/sms_gateway/ajaxList',
                parseData: function(res) { //res 即为原始返回的数据
                    return {
                        "code": res.code, //解析接口状态
                        "msg": res.message, //解析提示文本
                        "count": res.data.count, //解析数据长度
                        "data": res.data.list //解析数据列表
                    }
                },
                cellMinWidth: 80,//全局定义常规单元格的最小宽度
                toolbar: '#toolbarColumn',//开启头部工具栏，并为其绑定左侧模板
                defaultToolbar: ['filter', 'exports', 'print', { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                    title: '搜索',
                    layEvent: 'TABLE_SEARCH',
                    icon: 'layui-icon-search'
                }],
                title: '短信服务商列表',
                cols: [[
                    {type: 'checkbox', align: 'center'},
                    {field:'id', title:'ID', width:80, align: 'center', unresize: true, sort: true},
                    {field:'code', title:'标识', align: 'center'},
                    {field:'remark', title:'服务商', align: 'center',
                        templet: function (info){
                            if(info.url.length > 0) {
                                return '<a href="' + info.url + '" target="_blank" style="color: #00a2d4">'+info.remark+'</a>';
                            }else{
                                return info.remark;
                            }
                        }
                    },
                    {field: 'is_default', title: '是否启用', width:120, align: 'center',
                        templet: function (info){
                            if(info.is_default == 1){
                                return '<input type="checkbox" id="is_default_'+info.id+'" name="is_default" value="'+info.id+'" lay-skin="switch" lay-text="是|否" lay-filter="isOpen" checked>'
                            }else{
                                return '<input type="checkbox" id="is_default_'+info.id+'" name="is_default" value="'+info.id+'" lay-skin="switch" lay-text="是|否" lay-filter="isOpen">'
                            }
                        }
                    },
                    {title:'操作',toolbar: '#barOperate', align: 'center'}
                ]],
                id: 'listReload',
                limits: [10, 20, 30, 50, 100,200],
                limit: 10,
                page: true,
                text: {
                    none: '抱歉！暂无数据~' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
                }
            });

            //头工具栏事件
            table.on('toolbar(tableList)', function(obj){
                switch(obj.event){
                    case 'TABLE_SEARCH': // 搜索功能
                        var display = $(".table-search-fieldset").css("display"); //获取标签的display属性
                        if(display == 'none'){
                            $(".table-search-fieldset").show();
                        }else{
                            $(".table-search-fieldset").hide();
                        }
                        break;
                };
            });

            // 监听行工具事件
            table.on('tool(tableList)', function(obj){
                var data = obj.data;
                var id = data.id;
                switch (obj.event){
                    case "edit":  // 编辑功能
                        var index = layer.open({
                            title: obj.data.remark+' - 编辑',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/sms_gateway/edit?id='+id,
                        });
                        $(window).on("resize", function () {
                            layer.full(index);
                        });
                        break;
                }
            });

            // 监听搜索操作
            form.on('submit(data-search-btn)', function (data) {
                //执行搜索重载
                table.reload('listReload', {
                    where: data.field
                });
                return false;
            });

            // 监听重置操作
            form.on('submit(data-reset-btn)', function (data) {
                form.render();
            });

            //监听状态操作
            form.on('switch(isOpen)', function(obj){
                var checked = obj.elem.checked;
                var id = obj.value;
                $.ajax({
                    url:'/admin/sms_gateway/saveDefault',
                    type:'post',
                    data:{'is_default':checked,'id':id},
                    dataType:"JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(data){
                        if(data.code == 0){
                            layer.msg(data.message,{icon: 1,time:1500});
                        }else{
                            layer.msg(data.message,{icon: 2,time:1500},function(){
                                $('#is_default_'+id).prop('checked', !checked);
                                form.render('checkbox');
                            });
                        }
                    },
                    error: function (res){
                        layer.msg(res.statusText,{icon: 2,time:1500});
                    }
                });
            });

        });
    </script>
@endsection
