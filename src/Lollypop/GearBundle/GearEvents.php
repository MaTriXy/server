<?php

namespace Lollypop\GearBundle;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GearEvents
 *
 * @author seshachalam
 */
final class GearEvents
{
    //receive event
    const GCM_MSG_RECEIVED = 'gcm_msg.received';
    const GCM_ACK_RECEIVED = 'gcm_ack.received';
    const GCM_NACK_RECEIVED = 'gcm_nack.received';
    const GCM_RECEIPT_RECEIVED = 'gcm_receipt.received';
    const GCM_CONTROL_MSG_RECEIVED = 'gcm_control_msg.received';
    
    //sending events
    const GCM_MSG_PUSHED = 'gcm_msg.pushed';

}
