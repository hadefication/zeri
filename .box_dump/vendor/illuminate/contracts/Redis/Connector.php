<?php

namespace Illuminate\Contracts\Redis;

interface Connector
{







public function connect(array $config, array $options);









public function connectToCluster(array $config, array $clusterOptions, array $options);
}
