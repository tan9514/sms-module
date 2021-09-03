<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSmsGatewaysTable extends Migration
{
    public $tableName = "sms_gateways";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName)) $this->create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    /**
     * 执行创建表
     */
    private function create()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';      // 设置存储引擎
            $table->charset = 'utf8';       // 设置字符集
            $table->collation  = 'utf8_general_ci';       // 设置排序规则

            $table->id();
            $table->string('code', 100)->nullable(false)->comment("短信服务商CODE标识")->unique("code_unique");
            $table->longText("params")->nullable(false)->comment("短信服务商配置参数");
            $table->string("param_view", 100)->nullable(false)->default("gateway_params_common")->comment("配置参数格式页面预览");
            $table->tinyInteger("is_default")->nullable(false)->default(0)->comment("是否启用：0=否 1=是")->index("is_default_index");
            $table->string("remark")->nullable(false)->default("")->comment("描述");
            $table->string("url")->nullable(false)->default("")->comment("服务商官网");
            $table->timestamps();
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '短信服务商表'";
        DB::statement($qu);
    }
}
