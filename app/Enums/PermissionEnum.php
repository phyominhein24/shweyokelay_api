<?php

namespace App\Enums;


enum PermissionEnum: string
{

    /** VEHICLES_TYPE */
    case VEHICLES_TYPE_INDEX = 'Vehicles_Type_All';
    case VEHICLES_TYPE_SHOW = 'Vehicles_Type_Detail';
    case VEHICLES_TYPE_STORE = 'Vehicles_Type_Create';
    case VEHICLES_TYPE_UPDATE = 'Vehicles_Type_Update';
    case VEHICLES_TYPE_DESTROY = 'Vehicles_Type_Delete';

    /** MEMBER */
    case MEMBER_INDEX = 'Member_All';
    case MEMBER_SHOW = 'Member_Detail';
    case MEMBER_STORE = 'Member_Create';
    case MEMBER_UPDATE = 'Member_Update';
    case MEMBER_DESTROY = 'Member_Delete';

    /** AGENT */
    case AGENT_INDEX = 'Agent_All';
    case AGENT_SHOW = 'Agent_Detail';
    case AGENT_STORE = 'Agent_Create';
    case AGENT_UPDATE = 'Agent_Update';
    case AGENT_DESTROY = 'Agent_Delete';

    /** ROLE */
    case ROLE_INDEX = 'Role_All';
    case ROLE_SHOW = 'Role_Detail';
    case ROLE_STORE = 'Role_Create';
    case ROLE_UPDATE = 'Role_Update';
    case ROLE_DESTROY = 'Role_Delete';

    /** COUNTER */
    case COUNTER_INDEX = 'Counter_All';
    case COUNTER_SHOW = 'Counter_Detail';
    case COUNTER_STORE = 'Counter_Create';
    case COUNTER_UPDATE = 'Counter_Update';
    case COUNTER_DESTROY = 'Counter_Delete';

    /** USER */
    case USER_INDEX = 'User_All';
    case USER_SHOW = 'User_Detail';
    case USER_STORE = 'User_Create';
    case USER_UPDATE = 'User_Update';
    case USER_DESTROY = 'User_Delete';

    /** PAYMENT_HISTORY */
    case PAYMENT_HISTORY_INDEX = 'Payment_History_All';
    case PAYMENT_HISTORY_SHOW = 'Payment_History_Detail';
    case PAYMENT_HISTORY_STORE = 'Payment_History_Create';
    case PAYMENT_HISTORY_UPDATE = 'Payment_History_Update';
    case PAYMENT_HISTORY_DESTROY = 'Payment_History_Delete';

    /** ROUTES */
    case ROUTES_INDEX = 'Routes_All';
    case ROUTES_SHOW = 'Routes_Detail';
    case ROUTES_STORE = 'Routes_Create';
    case ROUTES_UPDATE = 'Routes_Update';
    case ROUTES_DESTROY = 'Routes_Delete';

    /** PERMISSION */
    case PERMISSION_INDEX = 'Permission_All';
    case PERMISSION_SHOW = 'Permission_Detail';

    /** AUTH */
    case AUTH_UPDATE = 'Auth_Update';
    case DASHBOARD_INDEX = 'Dashboard_All';
}
