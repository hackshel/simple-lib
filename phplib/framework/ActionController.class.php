<?php
/**
 * Action controller, an action of providing uri router service.
 *
 * @package    Framework
 * @version    v1.0.0
 */

class ActionController extends Action
{
    protected $ruleConfig = array();
    protected $hashMapping = array();
    protected $prefixMapping = array();
    protected $regexMapping = array();

    /**
     * Initialize the action controller
     *
     * @param array $config Uri router config
     * @return bool
     */
    public function initial($config)
    {
        $this->ruleConfig = isset($config['rule_config']) ? $config['rule_config'] : array();
        $this->hashMapping = isset($config['hash_mapping']) ? $config['hash_mapping'] : array();
        $this->prefixMapping = isset($config['prefix_mapping']) ? $config['prefix_mapping'] : array();
        $this->regexMapping = isset($config['regex_mapping']) ? $config['regex_mapping'] : array();
        return true;
    }

    /**
     * Start execution of the action controller.
     *
     * @param Context $context Context object for all the actions in action chain
     * @param array $actionParams Params for the action
     * @return bool Ture if the action has finish the request proccessing, or false if otherwise
     */
    public function execute(Context $context, array $actionParams = array())
    {
        $info = $this->getDispatchedActionInfo($context);
        if ($info) {
            if (is_array($info[1])) {
                $actionParams = array_merge($info[1], $actionParams);
            }
            return $context->callAction($info[0]->actionClassName, $actionParams);
        }
        return false;
    }

    /**
     * Get the dispatched action's config
     * @param Context $context
     * @return array
     */
    private function getDispatchedActionInfo(Context $context)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = '';
        }

        $ignoredDirs = isset($this->ruleConfig['begindex']) ? intval($this->ruleConfig['begindex']) : 0;
        $parsedUri = $this->parseRequestUri($uri, $ignoredDirs);

        // Always use hash mapping rules to dispatch uri as the first selection
        if (isset($this->hashMapping[$parsedUri])) {
            $actionConfig = $this->hashMapping[$parsedUri];
            $actionParams = isset($actionConfig[1]) ? $actionConfig[1] : array();
            $action = $context->getAction($actionConfig[0], $actionParams);
            return array($action, null);
        }

        // If no hash mapping rule matched, use prefix mapping rules as the second selection
        foreach ($this->prefixMapping as $pattern => $actionConfig) {
            if (strpos($parsedUri, $pattern) === 0) {
                $actionParams = isset($actionConfig[1]) ? $actionConfig[1] : array();
                $action = $context->getAction($actionConfig[0], $actionParams);
                return array($action, null);
            }
        }

        // Use regex mapping rule as the last selection
        foreach ($this->regexMapping as $pattern => $actionConfig) {
            if (preg_match($pattern, $uri, $matches)) {
                $actionParams = isset($actionConfig[1]) ? $actionConfig[1] : array();
                $action = $context->getAction($actionConfig[0], $actionParams);
                return array($action, $matches);
            }
        }

        $errmsg = 'No action could be dispatched for uri: ' . $uri;
        if (class_exists('CLog')) {
            CLog::warning($errmsg);
        } else {
            trigger_error($errmsg, E_USER_WARNING);
        }
        return null;
    }

    /**
     * Parese request uri and ignore some prefix dirs.
     *
     * @param string $uri Uri to be paresed
     * @param int $ignoredDirs How many dirs to be ignored
     * @return string
     */
    private function parseRequestUri($uri, $ignoredDirs = 0)
    {
        if (!isset($ignoredDirs) || $ignoredDirs < 0) {
            $ignoredDirs = 0;
        }

        $path = explode('?', $uri);
        $path = explode('/', $path[0]);

        $dirs = array();
        foreach ($path as $value) {
            $value = trim($value);
            if ('' === $value) {
                continue;
            }
            $dirs[] = $value;
        }

        $dirs = array_slice($dirs, $ignoredDirs);
        $uri = '/' . implode('/', $dirs);

        return strtolower($uri);
    }
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
