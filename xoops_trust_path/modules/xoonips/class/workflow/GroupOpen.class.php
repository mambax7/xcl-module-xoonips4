<?php

require_once XOONIPS_TRUST_PATH.'/class/core/Workflow.class.php';
require_once XOONIPS_TRUST_PATH.'/class/core/WorkflowClientBase.class.php';
require_once XOONIPS_TRUST_PATH.'/class/core/BeanFactory.class.php';
require_once dirname(dirname(__FILE__)).'/core/User.class.php';

class Xoonips_WorkflowClientGroupOpen extends Xoonips_WorkflowClientBase
{
    public $dataname = Xoonips_Enum::WORKFLOW_GROUP_OPEN;

    public function doCertify($gid, $comment)
    {
        $groupBean = Xoonips_BeanFactory::getBean('GroupsBean', $this->dirname, $this->trustDirname);
        if (!$groupBean->groupsOpen($gid)) {
            return false;
        }
        $group_handler = &xoops_gethandler('group');
        $xoopsGroup = $group_handler->get($gid);
        //event log
        $user = Xoonips_User::getInstance();
        $user->doGroupOpened($gid, $xoopsGroup, false, $comment);
    }

    public function doProgress($gid)
    {
        $sendToUsers = Xoonips_Workflow::getCurrentApproverUserIds($this->dirname, $this->dataname, $gid);
        $this->notification->groupOpenRequest($gid, $sendToUsers);
    }

    public function doRefuse($gid, $comment)
    {
        $groupBean = Xoonips_BeanFactory::getBean('GroupsBean', $this->dirname, $this->trustDirname);
        if (!$groupBean->groupsClose($gid)) {
            return false;
        }
        $group_handler = &xoops_gethandler('group');
        $xoopsGroup = $group_handler->get($gid);
        //event log
        XCube_DelegateUtils::call('Module.Xoonips.Event.Group.OpenReject', $xoopsGroup);

        //send to group admins and certifyUsers
        $groupUserLinkBean = Xoonips_BeanFactory::getBean('GroupsUsersLinkBean', $this->dirname, $this->trustDirname);
        $sendToUsers = array();
        foreach ($groupUserLinkBean->getAdminUserIds($gid) as $id) {
            $sendToUsers[] = $id;
        }
        $sendToUsers = array_merge($sendToUsers, Xoonips_Workflow::getAllApproverUserIds($this->dirname, $this->dataname, $gid));
        $sendToUsers = array_unique($sendToUsers);
        $this->notification->groupOpenRejected($gid, $sendToUsers, $comment);
    }
}
