<?php

require_once 'controllers/template.controller.php';
require_once 'controllers/user.controller.php';
require_once 'controllers/categories.controller.php';
require_once 'controllers/product.controller.php';
require_once 'controllers/tax.controller.php';
require_once 'controllers/discount.controller.php';
require_once 'controllers/payments.controller.php';
require_once 'controllers/supplier.controller.php';
require_once 'controllers/expenses.controller.php';
require_once 'controllers/orders.controller.php';
require_once 'controllers/returns.controller.php';
require_once 'controllers/notifications.controller.php';
require_once 'controllers/activitylog.controller.php';
require_once 'controllers/store.controller.php';
require_once 'controllers/loyalty.controller.php';
require_once 'controllers/packagevalidate.controller.php';
require_once 'controllers/customer.controller.php';
require_once 'controllers/reports.controller.php';


require_once 'models/user.models.php';
require_once 'models/categories.models.php';
require_once 'models/product.model.php';
require_once 'models/tax.models.php';
require_once 'models/discount.models.php';
require_once 'models/payment.model.php';
require_once 'models/supplier.model.php';
require_once 'models/expenses.model.php';
require_once 'models/orders.model.php';
require_once 'models/returns.model.php';
require_once 'models/notifications.model.php';
require_once 'models/activitylog.model.php';
require_once 'models/store.model.php';
require_once 'models/loyalty.model.php';
require_once 'models/packagevalidate.model.php';
require_once 'models/customer.model.php';
require_once 'models/reports.model.php';

$template= new templateController();
$template-> ctrTemplate();
