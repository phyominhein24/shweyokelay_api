<?php

namespace App\Enums;

enum PermissionEnum: string
{
    
    /** BILL */
    case BILL_INDEX = 'Bill_All';
    case BILL_SHOW = 'Bill_Detail';
    case BILL_STORE = 'Bill_Create';
    case BILL_UPDATE = 'Bill_Update';
    case BILL_DESTROY = 'Bill_Delete';

    /** CASHIER */
    case CASHIER_INDEX = 'Cashier_All';
    case CASHIER_SHOW = 'Cashier_Detail';
    case CASHIER_STORE = 'Cashier_Create';
    case CASHIER_UPDATE = 'Cashier_Update';
    case CASHIER_DESTROY = 'Cashier_Delete';

    /** CATEGORY */
    case CATEGORY_INDEX = 'Category_All';
    case CATEGORY_SHOW = 'Category_Detail';
    case CATEGORY_STORE = 'Category_Create';
    case CATEGORY_UPDATE = 'Category_Update';
    case CATEGORY_DESTROY = 'Category_Delete';

    /** CUSTOMER */
    case CUSTOMER_INDEX = 'Customer_All';
    case CUSTOMER_SHOW = 'Customer_Detail';
    case CUSTOMER_STORE = 'Customer_Create';
    case CUSTOMER_UPDATE = 'Customer_Update';
    case CUSTOMER_DESTROY = 'Customer_Delete';

    /** INVOICE */
    case INVOICE_INDEX = 'Invoice_All';
    case INVOICE_SHOW = 'Invoice_Detail';
    case INVOICE_STORE = 'Invoice_Create';
    case INVOICE_UPDATE = 'Invoice_Update';
    case INVOICE_DESTROY = 'Invoice_Delete';

    /** INVOICE_ITEM */
    case INVOICE_ITEM_INDEX = 'Invoice_Item_All';
    case INVOICE_ITEM_SHOW = 'Invoice_Item_Detail';
    case INVOICE_ITEM_STORE = 'Invoice_Item_Create';
    case INVOICE_ITEM_UPDATE = 'Invoice_Item_Update';
    case INVOICE_ITEM_DESTROY = 'Invoice_Item_Delete';

    /** ITEM */
    case ITEM_INDEX = 'Item_All';
    case ITEM_SHOW = 'Item_Detail';
    case ITEM_STORE = 'Item_Create';
    case ITEM_UPDATE = 'Item_Update';
    case ITEM_DESTROY = 'Item_Delete';

    /** ITEM_DATA */
    case ITEM_DATA_INDEX = 'Item_Data_All';
    case ITEM_DATA_SHOW = 'Item_Data_Detail';
    case ITEM_DATA_STORE = 'Item_Data_Create';
    case ITEM_DATA_UPDATE = 'Item_Data_Update';
    case ITEM_DATA_DESTROY = 'Item_Data_Delete';

    /** MATERIAL */
    case MATERIAL_INDEX = 'Material_All';
    case MATERIAL_SHOW = 'Material_Detail';
    case MATERIAL_STORE = 'Material_Create';
    case MATERIAL_UPDATE = 'Material_Update';
    case MATERIAL_DESTROY = 'Material_Delete';

    /** MATERIAL_DATA */
    case MATERIAL_DATA_INDEX = 'Material_Data_All';
    case MATERIAL_DATA_SHOW = 'Material_Data_Detail';
    case MATERIAL_DATA_STORE = 'Material_Data_Create';
    case MATERIAL_DATA_UPDATE = 'Material_Data_Update';
    case MATERIAL_DATA_DESTROY = 'Material_Data_Delete';

    /** ORDER */
    case ORDER_INDEX = 'Order_All';
    case ORDER_SHOW = 'Order_Detail';
    case ORDER_STORE = 'Order_Create';
    case ORDER_UPDATE = 'Order_Update';
    case ORDER_DESTROY = 'Order_Delete';

    /** PAYMENT */
    case PAYMENT_INDEX = 'Payment_All';
    case PAYMENT_SHOW = 'Payment_Detail';
    case PAYMENT_STORE = 'Payment_Create';
    case PAYMENT_UPDATE = 'Payment_Update';
    case PAYMENT_DESTROY = 'Payment_Delete';

    /** ROLE */
    case ROLE_INDEX = 'Role_All';
    case ROLE_SHOW = 'Role_Detail';
    case ROLE_STORE = 'Role_Create';
    case ROLE_UPDATE = 'Role_Update';
    case ROLE_DESTROY = 'Role_Delete';

    /** SHOP */
    case SHOP_INDEX = 'Shop_All';
    case SHOP_SHOW = 'Shop_Detail';
    case SHOP_STORE = 'Shop_Create';
    case SHOP_UPDATE = 'Shop_Update';
    case SHOP_DESTROY = 'Shop_Delete';

    /** TABLE_NUMBER */
    case TABLE_NUMBER_INDEX = 'Table_Number_All';
    case TABLE_NUMBER_SHOW = 'Table_Number_Detail';
    case TABLE_NUMBER_STORE = 'Table_Number_Create';
    case TABLE_NUMBER_UPDATE = 'Table_Number_Update';
    case TABLE_NUMBER_DESTROY = 'Table_Number_Delete';

    /** TRANSFER_ITEM */
    case TRANSFER_ITEM_INDEX = 'Transfer_Item_All';
    case TRANSFER_ITEM_SHOW = 'Transfer_Item_Detail';
    case TRANSFER_ITEM_STORE = 'Transfer_Item_Create';
    case TRANSFER_ITEM_UPDATE = 'Transfer_Item_Update';
    case TRANSFER_ITEM_DESTROY = 'Transfer_Item_Delete';

    /** TRANSFER_MATERIAL */
    case TRANSFER_MATERIAL_INDEX = 'Transfer_Material_All';
    case TRANSFER_MATERIAL_SHOW = 'Transfer_Material_Detail';
    case TRANSFER_MATERIAL_STORE = 'Transfer_Material_Create';
    case TRANSFER_MATERIAL_UPDATE = 'Transfer_Material_Update';
    case TRANSFER_MATERIAL_DESTROY = 'Transfer_Material_Delete';

    /** USER */
    case USER_INDEX = 'User_All';
    case USER_SHOW = 'User_Detail';
    case USER_STORE = 'User_Create';
    case USER_UPDATE = 'User_Update';
    case USER_DESTROY = 'User_Delete';

    /** SALE_MAN_WORKING_TIME */
    case SALE_MAN_WORKING_TIME_INDEX = 'Sale_Work_Time_All';
    case SALE_MAN_WORKING_TIME_SHOW = 'Sale_Work_Time_Detail';
    case SALE_MAN_WORKING_TIME_STORE = 'Sale_Work_Time_Create';
    case SALE_MAN_WORKING_TIME_UPDATE = 'Sale_Work_Time_Update';
    case SALE_MAN_WORKING_TIME_DESTROY = 'Sale_Work_Time_Delete';

    /** PERMISSION */
    case PERMISSION_INDEX = 'Permission_All';
    case PERMISSION_SHOW = 'Permission_Detail';

    /** AUTH */
    case AUTH_UPDATE = 'Auth_Update';
    case DASHBOARD_INDEX = 'Dashboard_All';
}
