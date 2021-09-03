@extends('admin.public.header')
@section('title','发送记录列表')

@section('listsearch')
    <fieldset class="table-search-fieldset" style="display:none">
        <legend>搜索信息</legend>
        <div style="margin: 10px 10px 10px 10px">
            <form class="layui-form layui-form-pane form-search" action="" id="searchFrom">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">全部</option>
                                @foreach($statusArr as $statusk => $status)
                                    <option value="{{$statusk}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">服务商</label>
                        <div class="layui-input-inline">
                            <select name="gateway_id">
                                <option value="">全部</option>
                                @foreach($gatewayArr as $gateway)
                                    <option value="{{$gateway->id}}">{{$gateway->remark}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <br>

                    <div class="layui-inline">
                        <label class="layui-form-label">接收号码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone" placeholder="接收号码查询" class="layui-input" />
                        </div>
                    </div>

                    <br>

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
            <button class="layui-btn layui-btn-sm layui-bg-red" lay-event="delete"><i class="layui-icon layui-icon-delete"></i>批量删除</button>
        </div>
    </script>
    <!-- 操作按钮 -->
    <script type="text/html" id="barOperate">
    </script>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['form','table','laydate'], function(){
            var table = layui.table, $=layui.jquery, form = layui.form, laydate = layui.laydate;
            // 渲染表格
            table.render({
                elem: '#tableList',
                url:'/admin/sms_log/ajaxList',
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
                defaultToolbar: [
                    'filter',
                    'exports',
                    'print',
                    { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                        title: '搜索',
                        layEvent: 'TABLE_SEARCH',
                        icon: 'layui-icon-search'
                    }
                ],
                title: '短信模板列表',
                cols: [[
                    {type: 'checkbox', align: 'center'},
                    {field:'id', title:'ID', width:80, align: 'center', unresize: true, sort: true},
                    {field:'gateway_name', title:'服务商', width:100, align: 'center',
                        templet: function (info){
                            var url = "{{url('admin/index/index#//admin/sms_gateway/list')}}";
                            return '<a href="'+url+'" target="_blank" style="color: #00a2d4">'+info.gateway_name+'</a>';
                        }
                    },
                    {field:'phone', title:'接收电话', width:120, align: 'center'},
                    {field:'status_name', title:'状态', width:160, align: 'center',
                        templet: function (info){
                            return info.status_name + "<p>" + info.created_at + "</p>";
                        }
                    },
                    {field:'send_params', title:'日志内容',
                        templet: function (info){
                            return setJsonText(info.send_params);
                        }
                    },
                    {field:'result_params', title:'返回内容',
                        templet: function (info){
                            return setJsonText(info.result_params);
                        }
                    },
                    // {title:'操作',toolbar: '#barOperate', align: 'center'}
                ]],
                id: 'listReload',
                limits: [5, 10, 20, 30, 50, 100, 200],
                limit: 5,
                page: true,
                text: {
                    none: '抱歉！暂无数据~' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
                }
            });

            //头工具栏事件
            table.on('toolbar(tableList)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id);
                var ids = [];
                var data = checkStatus.data;
                for (var i=0;i<data.length;i++){
                    ids.push(data[i].id);
                }
                switch(obj.event){
                    case 'delete':
                        if(!ids.length){
                            return layer.msg('请勾选要删除的数据',{icon: 2});
                        }
                        layer.confirm('确定删除选中的数据吗？', {
                            title : "操作确认",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/sms_log/del',
                                type:'post',
                                data:{'ids':ids},
                                dataType:"JSON",
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success:function(data){
                                    if(data.code == 0){
                                        layer.msg(data.message,{icon: 1,time:1500},function(){
                                            table.reload('listReload');
                                        });
                                    }else{
                                        layer.msg(data.message,{icon: 2});
                                    }
                                },
                                error:function(e){
                                    layer.msg(data.message,{icon: 2});
                                },

                            });
                        });
                        break;

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

                }
            });

            // 监听搜索操作
            form.on('submit(data-search-btn)', function (data) {
                //执行搜索重载
                table.reload('listReload', {
                    where: data.field,
                    page: {
                        curr: 1
                    }
                });
                return false;
            });

            // // 监听重置操作
            // form.on('submit(data-reset-btn)', function (data) {
            //     form.render();
            // });
            //
            // //监听状态操作
            // form.on('switch(isOpen)', function(obj){
            //     var checked = obj.elem.checked;
            //     var id = obj.value;
            //     $.ajax({
            //         url:'/admin/sms_gateway/saveDefault',
            //         type:'post',
            //         data:{'is_default':checked,'id':id},
            //         dataType:"JSON",
            //         headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //         success:function(data){
            //             if(data.code == 0){
            //                 layer.msg(data.message,{icon: 1,time:1500});
            //             }else{
            //                 layer.msg(data.message,{icon: 2,time:1500},function(){
            //                     $('#is_default_'+id).prop('checked', !checked);
            //                     form.render('checkbox');
            //                 });
            //             }
            //         },
            //         error: function (res){
            //             layer.msg(res.statusText,{icon: 2,time:1500});
            //         }
            //     });
            // });

            // 设置JSON数据页面展示格式
            function setJsonText(jsons){
                if(typeof jsons === "string") {
                    jsons = JSON.parse(jsons);
                }
                let text = "";
                function setReplaceText(arr, em){
                    let nem = em * 1;
                    let eem = (em - 1) * 1;
                    em += 1;
                    nem = nem + "em";
                    eem = eem + "em";
                    let nlen = arr.length;
                    let ntext = "";
                    if(nlen <= 0){
                        return "[]";
                    }else if(nlen === undefined) {
                        ntext = "{";
                        $.each(arr, function (i, v) {
                            if (typeof v == 'object') {
                                ntext += "<p style='text-indent:" + nem + "'>" + i + ": " + setReplaceText(v, em) + "</p>";
                            } else {
                                ntext += "<p style='text-indent:" + nem + "'>" + i + ": " + v + "</p>";
                            }
                        });
                        ntext += "<p style='text-indent:" + eem + "'>}</p>";
                    }else{
                        ntext = "[";
                        $.each(arr, function (i, v) {
                            if (typeof v == 'object') {
                                ntext += "<p style='text-indent:" + nem + "'>" + setReplaceText(v, em) + "</p>";
                            } else {
                                ntext += "<p style='text-indent:" + nem + "'>" + v + "</p>";
                            }
                        });
                        ntext += "<p style='text-indent:" + eem + "'>]</p>";
                    }
                    return ntext;
                }
                text += setReplaceText(jsons, 1);
                return text;
            }

        });
    </script>
@endsection
