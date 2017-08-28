<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: yangf
 * Date: 2017/5/6
 * Time: 19:30
 */


class ManageLimit extends CI_Controller
{
    private $lock = array(
        '正常','冻结'
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        echo $this->views->setRow(
            array(
                array(
                    'type'=>'button',
                    'col'=>3,
                    'desc'=>$this->lang->line('adjustment_quota'),
                    'onclick'=>array(
                        'type'=>'edit',
                        'field'=>array(
                            array(
                                'name'=>'_limit',
                                'desc'=>$this->lang->line('limit'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'=>'button',
                    'col'=>3,
                    'desc'=>$this->lang->line('clear_limit'),
                    'onclick'=>array(
                        'type'=>'delete',
                    ),
                ),
                array(
                    'type'=>'button',
                    'col'=>3,
                    'desc'=>$this->lang->line('frozen_client'),
                    'onclick'=>array(
                        'type'=>'delete1',
                    ),
                ),
                array(
                    'type'=>'button',
                    'col'=>3,
                    'desc'=>$this->lang->line('free_client'),
                    'onclick'=>array(
                        'type'=>'delete2',
                    ),
                ),
            )
        )->setRow(
            array(
                array(
                    'type'=>'table',
                    'thList'=>array('选择', '渠道号', '名称','额度', '状态'),
                    'colList'=>array('clientid', 'name','_limit', 'status'),
                ),
            )
        )->done();
    }

    public function ajaxTable(){

        $data = $this->dbapp->manage_sp_web_get_clientsub($_SESSION['authId']);

        $this->load->library('app/client', array($_SESSION['authId']));

        $clientId = $this->client->getClientId();

        //
        foreach ($data as $value) {
            if (isset($value['clientid']) && isset($value['_limit']) && $value['clientid'] != $clientId){
                $ajaxData[] = array(
                    'id' => $value['uid'],
                    'clientid' => $value['clientid'],
                    '_limit' => $value['_limit'],
                    'name'=>  $value['name'],
                    '_status'=>$value['_status'],
                    'status'=>$this->lock[$value['_status']]
                );
            }

        }


        $ajaxData = isset($ajaxData) ? $ajaxData : [];
        echo json_encode(array('data'=>$ajaxData), true);
    }

    private function wLog($user_key, $log_gold){
        $this->dbapp->manage_sp_web_log_i(44, 1, $user_key, $_SESSION[$this->config->item('USER_AUTH_KEY')], $_SESSION['email'], '', 0, $log_gold);
    }

    public function edit(){
        // 额度修改
        $limit = $this->datarece->post('_limit', true, '不允许空值');
        // 修改对象
        $ids = $this->datarece->ids();

        // 判断用户渠道,若为超管可无限编辑额度
        $this->load->library('app/client', array($_SESSION['authId']));
        if ($this->client->getLevel() == 0) {
            $msg = '';
            foreach ($ids as $id) {
                $ret = $this->dbapp->manage_sp_news1_company_u_limit_1($id, $limit);
                foreach ($_REQUEST['tData'] as $tDatum) {
                    if ($tDatum['id'] == $id){
                        $this->wLog($tDatum['clientid'], $limit);
                        $st = 1;
                        break;
                    }
                }
                if ($st != 1){
                    $this->wLog('未知渠道', $limit);
                }
                if (isset($ret['status']) && $ret['status'] == 0) {
                }else{
                    $msg .= $id . '添加失败;';
                }
            }
            $msg == '' ? execExit('添加成功') : execExit($msg);
        }elseif($this->client->getLevel() == 2){
            // 渠道商
            // 获取子渠道
            $clients = $data = $this->dbapp->manage_sp_web_get_clientsub($_SESSION['authId']);

            foreach ($clients as $value) {
                if (isset($value['clientid'])){
                    // uids列表
                    $clientArr[] = $value['uid'];
                    // uids和渠道号的对应列表
                    $subClient[$value['uid']] = array($value['_limit'],$value['clientid']);
                }
            }
            $this->load->library('app/client', array($_SESSION['authId']));

            if (!$this->client->getStatus()){
                execExit('账户已经被封禁');
            }

            $this->load->library('redisapp');
            $key = 'limit_' . $_SESSION['authId'];
            if($this->redisapp->sign($key, 10) === 1){
                //　获取该渠道的额度判断是否可以支付
                $client_limit = $this->client->getLimit();
                $send = count($ids) * $limit;
                if($send > $client_limit){
                    $this->redisapp->del($key);
                    execExit('额度不足');
                }
                foreach ($ids as $id) {
                    // 属于子渠道
                    if (in_array($id, $clientArr)){
                        // 二级渠道减少额度
                        $this->dbapp->manage_sp_news1_company_u_limit_1($_SESSION['authId'], 0 - $limit);
                        // 三级渠道增加额度
                        $this->dbapp->manage_sp_news1_company_u_limit_1($id, $limit);

                        $this->wLog($subClient[$id][1], $limit);
                    }
                    continue;
                }
                $this->redisapp->del($key);

                execExit('成功');
            }else{
                _exit('点击过于频繁');
            }
        }

    }

    // 封禁
    public function delete1(){
        $clientids = $this->datarece->ids('clientid');
        $msg = '';

        $this->load->library('app/client', array($_SESSION['authId']));

        foreach ($clientids as $clientid) {
            $this->client->clientChangeStatus($clientid, 1);
        }
        execExit('处理成功');
    }

    public function delete2(){
        $clientids = $this->datarece->ids('clientid');
        $msg = '';

        $this->load->library('app/client', array($_SESSION['authId']));

        foreach ($clientids as $clientid) {
            $this->client->clientChangeStatus($clientid, 0);
        }
        execExit('处理成功');
    }


    public function delete(){
        $ids = $this->datarece->ids();
        $msg = '';

        $this->load->library('app/client', array($_SESSION['authId']));

        if (!$this->client->getStatus()){
            execExit('账户已经被封禁');
        }

        $clients = $data = $this->dbapp->manage_sp_web_get_clientsub($_SESSION['authId']);
        foreach ($clients as $value) {
            if (isset($value['clientid'])){
                $clientArr[] = $value['uid'];
                $subClient[$value['uid']] = $value['clientid'];
            }
        }
        $this->load->library('redisapp');
        $key = 'limit_' . $_SESSION['authId'];
        if($this->redisapp->sign($key, 10) === 1){
            foreach ($ids as $id) {
                if (in_array($id, $clientArr)){
                    $this->load->library('app/client', array($id));
                    $limit = $this->client->getLimit();
                    if ($limit <= 0){
                        $this->redisapp->del($key);
                        execExit('该渠道额度为0');
                    }

                    $this->dbapp->manage_sp_news1_company_u_limit_1($_SESSION['authId'], $limit);
                    $ret = $this->dbapp->manage_sp_news1_company_u_limit($id, 0);
                    $this->wLog($subClient[$id], 0-$limit);
                    if (isset($ret['status']) && $ret['status'] == 0) {

                    }else{
                        $msg .= $id . '清空失败;';
                    }

                }
            }
        }
        $this->redisapp->del($key);
        $msg == '' ? execExit('清空成功') : execExit($msg);
    }
}