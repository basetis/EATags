<?php
class check_rate {
    protected $ci;

    function __construct()
    {
        $this->ci =& get_instance();
    }
    public function check_rate_limit()
    {
        $rate_alert = '';
        $this->ci->load->model('rate_limit_queue');
        $this->ci->rate_limit_queue->time_to_be_free = $this->ci->rate_limit_queue->get_last_time_to_be_free($this->ci->session->userdata('evernote_user_id'));

        if ($this->ci->rate_limit_queue->time_to_be_free != NULL) {
            $rate_alert = $this->ci->lang->line('account_rate_limit') . $this->ci->rate_limit_queue->time_to_be_free;
            $this->ci->session->set_flashdata('alert_rate_message', $rate_alert);
            log_message('debug', $rate_alert);
            return $rate_alert;
        }
        return;
    }
}
?>