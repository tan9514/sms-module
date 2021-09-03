<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSmsTempTable extends Migration
{
    public $tableName = "sms_temp";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable($this->tableName)) $this->create();
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
            $table->unsignedBigInteger('gateway_id')->nullable(false)->comment("服务商ID")->index("gateway_id_index");
            $table->string("temp_name", 100)->nullable(false)->comment("模板名称")->index("temp_name_unique");
            $table->string("temp_key", 100)->nullable(false)->comment("唯一标识")->unique("temp_key_unique");
            $table->string("temp_code", 50)->nullable(false)->comment("模板CODE")->index("temp_code_index");
            $table->longText('content')->nullable(false)->comment("模板内容");
            $table->json("variable")->nullable(false)->comment("模板变量");
            $table->tinyInteger("status")->nullable(false)->default(1)->comment("1=等待服务商审核  2=审核成功  3=审核失败")->index("gateway_id_status");
            $table->timestamps();

            // 设置外键
            $table->foreign('gateway_id', $this->tableName . "_ibfk_1")->references('id')->on('sms_gateways');
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '短信模板表'";
        DB::statement($qu);
    }
}
