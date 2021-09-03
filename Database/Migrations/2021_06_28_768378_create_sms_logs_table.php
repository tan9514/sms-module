<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSmsLogsTable extends Migration
{
    public $tableName = "sms_logs";

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
            $table->string("phone", 30)->nullable(false)->comment("接收短信号码")->index("phone_index");
            $table->string('sms_code', 10)->nullable(false)->default("")->comment("短信验证码");
            $table->json("send_params")->nullable(false)->comment("发送记录的所有参数，报错的时候用于参数检查");
            $table->json("result_params")->nullable(false)->comment("发送短息返回信息，报错的时候用于参数检查");
            $table->tinyInteger("status")->nullable(false)->default(1)->comment("1=等待发送短信  2=发送成功  3=发送失败")->index("status_index");
            $table->timestamps();
            $table->softDeletes();

            // 设置外键
            $table->foreign('gateway_id', $this->tableName . "_ibfk_1")->references('id')->on('sms_gateways');
        });
        $prefix = DB::getConfig('prefix');
        $qu = "ALTER TABLE " . $prefix . $this->tableName . " comment '短信发送记录表'";
        DB::statement($qu);
    }
}
