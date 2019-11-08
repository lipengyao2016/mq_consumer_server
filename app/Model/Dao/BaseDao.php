<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/7
 * Time: 11:01
 */

namespace App\Model\Dao;


use Swoft\Log\Helper\CLog;

class BaseDao
{

    protected $tableName;

    /**
     * BaseDao constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     */
/*    public function setTableName($tableName): void
    {
        $this->tableName = $tableName;
    }
  */



    /** 原生SQL执行
     * @param string $sql
     * @return array
     */
    public function doSql(string $sql): array
    {
        $unprepared = \Swoft\Db\DB::unprepared($sql);
        if ($unprepared) {
            return $unprepared;
        }else{
            return NULL;
        }
    }

    /** 一行数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'select'=>['id','mobile','username']
     * ]
     * @return array
     */
    public function getRow($data): array
    {
        CLog::info(__METHOD__.' called data:'.json_encode($data));
        $select = isset($data['select'])?$data['select']:'*';
        $where = isset($data['where'])?$data['where']:'';
        $orderby = isset($data['orderby'])?$data['orderby']:'';

        $model = \Swoft\Db\DB::table($this->tableName);
        if(!empty($where)){
            if(is_array($where))
                $model->where($where);
            else
                $model->whereRaw($where);
        }
        if(!empty($orderby))
            $model->orderByRaw($orderby);
        $item = $model->first($select);
        return empty($item)?NULL:$item;
    }

    /** 根据ID取一行数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'id'=>10,
     * 'select'=>['id','mobile','username']
     * ]
     * @return array
     */
    public function getById($data): array
    {
        $data['where'] = ['id' => $data['id']];
        return $this->getRow($data);
    }

    /** 一行中某个字段的值
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'field'=>'username'
     * ]
     * @return array
     */
    public function getOne($data): array
    {
        if(!isset($data['field'])||empty($data['field']))
            return null;
        $where = isset($data['where'])?$data['where']:'';
        $orderby = isset($data['orderby'])?$data['orderby']:'';

        $model = \Swoft\Db\DB::table($this->tableName);
        if(!empty($where)){
            if(is_array($where))
                $model->where($where);
            else
                $model->whereRaw($where);
        }
        if(!empty($orderby))
            $model->orderByRaw($orderby);
        $value = $model->value($data['field']);
        return empty($value)?NULL:$value;
    }

    /** 统计查询结果的总数
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']]
     * ]
     * @return array
     */
    public function getCount($data): array
    {
        $where = $data['where'];
        if(empty($where)) $where="1=1";
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where))
            $value = $model->where($where)->count();
        else
            $value = $model->whereRaw($where)->count();
        return empty($value)?NULL:$value;
    }

    /** 统计查询结果的总和
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']]
     * ]
     * @return array
     */
    public function getSum($data): array
    {
        $where = isset($data['where'])?$data['where']:'';
        if(!isset($data['field'])||empty($data['field']))
            return null;
        $field = $data['field'];
        if(empty($where)) $where="1=1";
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where))
            $value = $model->where($where)->sum($field);
        else
            $value = $model->whereRaw($where)->sum($field);
        return empty($value)?NULL:$value;
    }

    /** 获取数据表中某一列的数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'field'=>'username',
     * 'orderby'=>'id desc',
     * 'page'=>1,
     * 'limit'=>10
     * ]
     * @return array
     */
    public function getPluck($data):array
    {
        if(!isset($data['field'])||empty($data['field']))
            return null;
        $where = isset($data['where'])?$data['where']:'';
        $orderby = isset($data['orderby'])?$data['orderby']:'';
        $page = isset($data['page'])?$data['page']:'';
        $limit = isset($data['limit'])?$data['limit']:'';
        $groupby = isset($data['groupby'])?$data['groupby']:'';
        $having = isset($data['having'])?$data['having']:'';

        $model = \Swoft\Db\DB::table($this->tableName);
        if(!empty($where)){
            if(is_array($where))
                $model->where($where);
            else
                $model->whereRaw($where);
        }
        if(!empty($orderby))
            $model->orderByRaw($orderby);
        if(!empty($groupby))
            $model->groupBy($groupby);
        if(!empty($having))
            $model->havingRaw($having);
        if(!empty($page)&&!empty($limit))
            $model->forPage($page,$limit);
        $res = $model->pluck($data['field']);
        //$res['sql'] = $model->toSql();//打印SQL
        return count($res)==0?null:$res->toArray();
    }



    /** 获取数据表中列表数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'select'=>['id','mobile','username']
     * 'orderby'=>'id desc',
     * 'page'=>1,
     * 'limit'=>10
     * ]
     * @return array
     */
    public function getList($data)
    {
        $where = isset($data['where'])?$data['where']:'';
        $select = isset($data['select'])?$data['select']:'';
        $orderby = isset($data['orderby'])?$data['orderby']:'';
        $page = isset($data['page'])?$data['page']:'';
        $limit = isset($data['limit'])?$data['limit']:'';
        $groupby = isset($data['groupby'])?$data['groupby']:'';
        $having = isset($data['having'])?$data['having']:'';
        $join =  isset($data['join'])?$data['join']:'';

        $select=empty($select)?["*"]:$select;
        $model = \Swoft\Db\DB::table($this->tableName);
       /* if(!empty($join)){
            /**
             * $join=[
             * type =>  leftjoin/join
             * data =>  [
             *      user=>[left_val=>user_relation.d_uid,right_val=>user.id]
             *      user_role=>[left_val=>user.role_id,right_val=>user.role_id]
             *      ]
             * ]
             */
           /* if(isset($join['type'])&& $join['type']=="leftjoin"){
                foreach ($join['data'] as $table=>$v) {
                    $model->leftJoin($table, $v['left_val'], "=", $v['right_val']);
                }
            }else{
                foreach ($join['data'] as $v) {
                    $model->join($table, $v['left_val'], "=", $v['right_val']);
                }
            }
        }*/
        if(!empty($where)){
            if(is_array($where))
                $model->where($where);
            else
                $model->whereRaw($where);
        }
        if(!empty($orderby))
            $model->orderByRaw($orderby);
        if(!empty($groupby))
            $model->groupBy($groupby);
        if(!empty($having))
            $model->havingRaw($having);
        if(!empty($page)&&!empty($limit))
            $model->forPage($page,$limit);
        CLog::info(__METHOD__.' select:'.json_encode($select).' orderby:'.$orderby);
        $res = $model->get($select);
        print_r('sql:'.$model->toSql());
        return empty($res)?null:$res;
    }

    /** 获取数据表中所有数据（包括分页信息）
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'select'=>['id','mobile','username']
     * 'orderby'=>'id desc',
     * 'page'=>1,
     * 'limit'=>10
     * ]
     * @return array
     */
    public function getAll($data): array
    {
        $where = isset($data['where'])?$data['where']:'';
        $select = isset($data['select'])?$data['select']:'';
        $orderby = isset($data['orderby'])?$data['orderby']:'';
        $page = isset($data['page'])?$data['page']:'';
        $limit = isset($data['limit'])?$data['limit']:'';
        $groupby = isset($data['groupby'])?$data['groupby']:'';
        $having = isset($data['having'])?$data['having']:'';
        $page=empty($page)?1:$page;
        $limit=empty($limit)?10:$limit;
        //$select=empty($select)?"":$select;

        $model = \Swoft\Db\DB::table($this->tableName)->select($select);
     /*   if(!empty($join)){
            /**
             * $join=[
             * type =>  leftjoin/join
             * data =>  [
             *      user=>[left_val=>user_relation.d_uid,right_val=>user.id]
             *      user_role=>[left_val=>user.role_id,right_val=>user.role_id]
             *      ]
             * ]
             */
           /* if(isset($join['type'])&& $join['type']=="leftjoin"){
                foreach ($join['data'] as $table=>$v) {
                    $model->leftJoin($table, $v['left_val'], "=", $v['right_val']);
                }
            }else{
                foreach ($join['data'] as $v) {
                    $model->join($table, $v['left_val'], "=", $v['right_val']);
                }
            }
        }*/
        if(!empty($where)){
            if(is_array($where))
                $model->where($where);
            else
                $model->whereRaw($where);
        }
        if(!empty($orderby))
            $model->orderByRaw($orderby);
        if(!empty($groupby))
            $model->groupBy($groupby);
        if(!empty($having))
            $model->havingRaw($having);
        print_r('sql:'.$model->toSql());
        $res = $model->paginate($page,$limit,["*"]);
        return empty($res)?null:$res;
    }

    /** 新增数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'data'=>['tid'=>2,'username'=>'test0001','mobile'=>'13655554444']
     * ]
     * @return array
     */
    public function insert($data): array
    {
        if(!$data)
             return null;
        return \Swoft\Db\DB::table($this->tableName)->insertGetId($data);
    }

    /** 新增多条数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'data'=>[
     *      ['tid'=>2,'username'=>'test0001','mobile'=>'13655554444'],
     *      ['tid'=>2,'username'=>'test0002','mobile'=>'13655554445']
     *  ]
     * ]
     * @return array
     */
    public function insertBatch($data): bool
    {
        if(!$data)
            return null;
        array_walk($data,function (&$value)
        {
           $value['add_time'] = time();
        });
        CLog::info(__METHOD__.'   $data:'.json_encode($data));
        return \Swoft\Db\DB::table($this->tableName)->insert($data);
    }

    /** 更新数据
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'data'=>['tid'=>2,'username'=>'test0001','mobile'=>'13655554444']
     * ]
     * @return array
     */
    public function update($data): array
    {
        if(!isset($data['where'])||empty($data['where']))
            return null;
        $where=empty($where)?"1=1":$where;
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where)) {
            $res = $model->where($where)->update($data);
        }elseif((int)$where > 0){
            $res = $model->where(['id'=>(int)$where])->update($data);
        }else{
            $res = $model->whereRaw($where)->update($data);
        }
        return $res;
    }

    /** 保存数据（没有数据则新增，否则更新）
     * @param array   $data=
     * [
     * 'table'=>'user',
     * 'where'=>['tid'=>2,['username','like','test%']],
     * 'data'=>['tid'=>2,'username'=>'test0001','mobile'=>'13655554444']
     * ]
     * @return array
     */
    public function save($data): array
    {
        if(!isset($data['where'])||empty($data['where']))
            return null;
        $where=empty($where)?"1=1":$where;
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where)) {
            $condition = $where;
            $res = $model->updateOrInsert($condition,$data);
        }elseif((int)$where > 0) {
            $condition = array('id' => (int)$where);
            $res = $model->updateOrInsert($condition,$data);
        }else{
            $res = NULL;
        }

        return $res;
    }

    /**
     * @param array   $data
     * @return array
     */
    public function delete($data): array
    {
        if(!isset($data['where'])||empty($data['where']))
            return null;
        $data['limit'] = isset($data['limit'])?$data['limit']:'';
        $limit = empty($limit)?1:$limit;
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($data['where']))
            $res = $model->where($data['where'])->limit($limit)->delete();
        else
            $res = $model->whereRaw($data['where'])->limit($limit)->delete();
        return $res;
    }

    /**
     * @param array   $data
     * @return array
     */
    public function increment($data): array
    {

        if(!isset($data['where'])||empty($data['where']))
            return null;
        if(!isset($data['field'])||empty($data['field']))
            return null;
        $data['count'] = isset($data['count'])?$data['count']:'';
        $where=empty($where)?"1=1":$where;
        $count=empty($count)?1:$count;
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where)) {
            $res = $model->where($where)->increment($data['field'],$count);
        }elseif((int)$where > 0){
            $res = $model->where(['id'=>(int)$where])->increment($data['field'],$count);
        }else{
            $res = $model->whereRaw($where)->increment($data['field'],$count);
        }
        return $res;
    }

    /**
     * @param array   $data
     * @return array
     */
    public function decrement($data): array
    {
        if(!isset($data['where'])||empty($data['where']))
            return null;
        if(!isset($data['field'])||empty($data['field']))
            return null;
        $data['count'] = isset($data['count'])?$data['count']:'';
        $where=empty($where)?"1=1":$where;
        $count=empty($count)?1:$count;
        $model = \Swoft\Db\DB::table($this->tableName);
        if(is_array($where)) {
            $res = $model->where($where)->decrement($data['field'],$count);
        }elseif((int)$where > 0){
            $res = $model->where(['id'=>(int)$where])->decrement($data['field'],$count);
        }else{
            $res = $model->whereRaw($where)->decrement($data['field'],$count);
        }
        return $res;
    }

}