<?php

namespace Hatch\Nacos\Service;

class NamespaceCenter extends BaseService
{
    /** @var string 查询命名空间列表 */
    const API_GET_NAMESPACES =  '/console/namespaces';
    /** @var string 创建命名空间 */
    const API_POST_NAMESPACE =  '/console/namespaces';
    /** @var string 修改命名空间 */
    const API_UPDATE_NAMESPACE =  '/console/namespaces';
    /** @var string 删除命名空间 */
    const API_DELETE_NAMESPACE =  '/console/namespaces';
}
