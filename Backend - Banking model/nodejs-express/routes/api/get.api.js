const router = require("express").Router();
const get = require("../../controllers/get.controller");

router.post("/currentbalance", get.validateCurrentbalance(), get.currentbalance);
router.post("/transactionhistory", get.validateTransactionhistory(), get.transactionhistory);

module.exports = router;
