<?php

namespace Hatch\Nacos\Service;

class ServerDiscover extends BaseService
{
    /** @var string 注册实例 */
    const API_REGISTER_INSTANCE =  '/ns/instance';
    /** @var string 注销实例 */
    const API_DESTROY_INSTANCE =  '/ns/instance';
    /** @var string 修改实例 */
    const API_UPDATE_INSTANCE =  '/ns/instance';
    /** @var string 查询实例列表 */
    const API_INSTANCE_LIST =  '/ns/instance/list';
    /** @var string 查询实例详情 */
    const API_INSTANCE_INFO =  '/ns/instance';
    /** @var string 发送实例心跳 */
    const API_INSTANCE_BEAT =  '/ns/instance/beat';
    /** @var string 创建服务 */
    const API_POST_SERVICE =  '/ns/service';
    /** @var string 删除服务 */
    const API_DELETE_SERVICE =  '/ns/service';
    /** @var string 更新服务 */
    const API_UPDATE_SERVICE =  '/ns/service';
    /** @var string 查询服务 */
    const API_GET_SERVICE =  '/ns/service';
    /** @var string 查询服务列表 */
    const API_SERVICE_LIST =  '/ns/service/list';
    /** @var string 查询系统开关 */
    const API_GET_SWITCH =  '/ns/operator/switches';
    /** @var string 修改系统开关 */
    const API_UPDATE_SWITCH =  '/ns/operator/switches';
    /** @var string 查看系统当前数据指标 */
    const API_GET_METRICS =  '/ns/operator/metrics';
    /** @var string 查看当前集群Server列表 */
    const API_NODES_SERVER =  '/ns/operator/servers';
    /** @var string 查看当前集群leader */
    const API_NODES_LEADER =  '/ns/raft/leader';
    /** @var string 更新实例的健康状态 */
    const API_INSTANCE_HEALTH =  '/ns/health/instance';


}
