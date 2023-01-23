<?php

	namespace DS\Despatchtimer\Model;
	
	use Magento\Framework\Model\AbstractModel;
	use Magento\Store\Model\ScopeInterface;
	
	class Timer extends AbstractModel {
	
		protected $_scopeConfig;
	
		public function __construct(
        \Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $fregistry,
        \Magento\Framework\View\Element\Template\Context $bcontext,		
        array $data = []
		) {
			parent::__construct($context, $fregistry);
			$this->_scopeConfig = $bcontext->getScopeConfig();
		}
		
		public function getDespatchTime() {
			$next_despatch_time = 0;
			$cfg_timezone = $this->_scopeConfig->getValue('general/locale/timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$curr_timezone = date_default_timezone_get();
			if($cfg_timezone) {
				date_default_timezone_set($cfg_timezone);			
			}
			//office on sat
			$off_on_sat = $this->_scopeConfig->getValue('despatchtimer/settings/off_on_sat', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			//office on sun
			$off_on_sun = $this->_scopeConfig->getValue('despatchtimer/settings/off_on_sun', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$despatch_time = $this->_scopeConfig->getValue('despatchtimer/settings/despatchtime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$desp_hour = 0;
			$desp_min = 0;
			if(strpos($despatch_time, ":") !== false) {
				$timer_arr = explode(":", $despatch_time);
				$desp_hour = $timer_arr[0];
				$desp_min = $timer_arr[1];
			}
			
			$day_number = date("N", strtotime('+1 day'));
			$curr_day_num = date("N", time());
			$current_hour = date("H");
			$current_min = date("i");
			
			//////////////////////////////////
			
			if($current_hour < $desp_hour) {
				if(($off_on_sat && $curr_day_num == 6 ) || ($off_on_sun && $curr_day_num == 7) || ($curr_day_num!=6 && $curr_day_num != 7)) { //for same day despatch
						$next_despatch_day_date = date("j", time());
						$next_despatch_day_mth = date("n", time());
						$next_despatch_day_year = date("Y", time());
						$today_desp_time = mktime($desp_hour, $desp_min, 0, $next_despatch_day_mth, $next_despatch_day_date, $next_despatch_day_year);
						$next_despatch_time = $today_desp_time - time();
				} elseif($curr_day_num == 6 && !$off_on_sat) { //if today is sat and no despatch
						if(!$off_on_sun) {
							$next_despatch_time = $this->getNextDaysTime(2, $desp_hour, $desp_min);
						} else { //office on sunday
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
						}
				} else { //curr day is Sun and no despatch
					$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
				}
			} elseif($current_hour == $desp_hour) { 
				if($current_min <= $desp_min) {
					$next_despatch_day_date = date("j",  time());
					$next_despatch_day_mth = date("n",  time());
					$next_despatch_day_year = date("Y",  time());
					$rem_time = mktime($desp_hour, $desp_min, 0, $next_despatch_day_mth, $next_despatch_day_date, $next_despatch_day_year);
					$next_despatch_time = $rem_time - time();
				} else {
					if($curr_day_num ==5) { //friday
						if(!$off_on_sat && !$off_on_sun) {
							$next_despatch_time = $this->getNextDaysTime(3, $desp_hour, $desp_min);
						} elseif($off_on_sat) {
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
						} elseif($off_on_sun) {
							$next_despatch_time = $this->getNextDaysTime(2, $desp_hour, $desp_min);
						}
					} elseif($curr_day_num == 6) { //sat
						if($off_on_sun) {
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
						} else { //next despatch is on weekday
							$next_despatch_time = $this->getNextDaysTime(2, $desp_hour, $desp_min);
						}
					} else {	//current_min>desp_min and curr_day_num is not fri/sat
						$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
					}
				}
			} else { //if curr_hr > despatch_hour
				if($curr_day_num != 5) {
					if($curr_day_num == 6 || $curr_day_num == 7) {
						if($curr_day_num == 6) { //saturday
							if($off_on_sun) {
								$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
							} else {
								$next_despatch_time = $this->getNextDaysTime(2, $desp_hour, $desp_min);
							}
						} else { //sunday
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
						}
					} else { //curr day is not Fri, Sat, or Sun
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
					}
				} else { //curr_day is friday and curr_hr > despatch_hour
					if($off_on_sat) {
							$next_despatch_time = $this->getNextDaysTime(1, $desp_hour, $desp_min);
					} elseif($off_on_sun) {
							$next_despatch_time = $this->getNextDaysTime(2, $desp_hour, $desp_min);
					} else { //next despatch is on Mon
						$next_despatch_time = $this->getNextDaysTime(3, $desp_hour, $desp_min);
					}
				}
			}
			
			/////////////////////////////////
			
			date_default_timezone_set($curr_timezone);
			
			return $next_despatch_time;
		}
		
		public function getNextDaysTime($num_days, $desp_hour, $desp_min) {
			if($num_days == 3) {
				$next_despatch_day_date = date("j",  strtotime("+3 day"));		
				$next_despatch_day_mth = date("n",  strtotime("+3 day"));
				$next_despatch_day_year = date("Y",  strtotime("+3 day"));
				$next_day_time = mktime($desp_hour, $desp_min, 0, $next_despatch_day_mth, $next_despatch_day_date, $next_despatch_day_year);
				$next_despatch_time = $next_day_time - time();
			} elseif($num_days == 2) {
				$next_despatch_day_date = date("j",  strtotime("+2 day"));		
				$next_despatch_day_mth = date("n",  strtotime("+2 day"));
				$next_despatch_day_year = date("Y",  strtotime("+2 day"));
				$next_day_time = mktime($desp_hour, $desp_min, 0, $next_despatch_day_mth, $next_despatch_day_date, $next_despatch_day_year);
				$next_despatch_time = $next_day_time - time();
			} else {
				$next_despatch_day_date = date("j",  strtotime("+1 day"));		
				$next_despatch_day_mth = date("n",  strtotime("+1 day"));
				$next_despatch_day_year = date("Y",  strtotime("+1 day"));
				$next_day_time = mktime($desp_hour, $desp_min, 0, $next_despatch_day_mth, $next_despatch_day_date, $next_despatch_day_year);
				$next_despatch_time = $next_day_time - time();
			}
			
			return $next_despatch_time;
		}
	
	}
