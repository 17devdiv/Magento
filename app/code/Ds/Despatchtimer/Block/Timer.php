<?php
namespace DS\Despatchtimer\Block;
 
class Timer extends \Magento\Framework\View\Element\Template
{
	protected $_modelFactory;
	
	protected $_scopeConfig;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\DS\Despatchtimer\Model\Timer $modelFactory,
		array $data = []
	) {
			$this->_modelFactory = $modelFactory;
			parent::__construct($context, $data);
	}

	
    public function displayTimer() {
		//$timer_model = $this->_modelfactory->create();
		$next_despatch_time = $this->_modelFactory->getDespatchTime();
		//$next_despatch_time = 86400;
		return $next_despatch_time;
	}
	
	public function getConfigValue($str_config_path) {
		$val = $this->_scopeConfig->getValue($str_config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $val;
	}
}
