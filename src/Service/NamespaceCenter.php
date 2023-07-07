<?php

namespace Hatch\Nacos\Service;

use GuzzleHttp\RequestOptions;
use Hatch\Nacos\Exception\AuthException;

class NamespaceCenter extends BaseService
{
    /**
     * 查询命名空间列表
     * @return mixed|string
     * @throws AuthException
     */
    public function getList()
    {
        $url = $this->buildRequestUrl('/console/namespaces');
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 创建命名空间
     * @param string $namespace_id
     * @param string $namespace_name
     * @param string $namespace_desc
     * @return mixed|string
     * @throws AuthException
     */
    public function create(string $namespace_id, string $namespace_name, string $namespace_desc = '')
    {
        $params = [
            'customNamespaceId' => $namespace_id,
            'namespaceName' => $namespace_name,
            'namespaceDesc' => $namespace_desc,
        ];
        $url = $this->buildRequestUrl('/console/namespaces', $params);
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'POST', $options);
    }

    /**
     * 更新命名空间
     * @param string $namespace_id
     * @param string $namespace_name
     * @param string $namespace_desc
     * @return mixed|string
     * @throws AuthException
     */
    public function update(string $namespace_id, string $namespace_name, string $namespace_desc = '')
    {
        $params = [
            'namespace' => $namespace_id,
            'namespaceShowName' => $namespace_name,
            'namespaceDesc' => $namespace_desc,
        ];
        $url = $this->buildRequestUrl('/console/namespaces', $params);
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }

    /**
     * 删除命名空间
     * @param string $namespace_id
     * @return mixed|string
     * @throws AuthException
     */
    public function destroy(string $namespace_id)
    {
        $params = [
            'namespaceId' => $namespace_id,
        ];
        $url = $this->buildRequestUrl('/console/namespaces', $params);
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'DELETE', $options);
    }
}
