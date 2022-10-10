const router = require("express").Router();
const get = require("../../controllers/get.controller");

router.get("/currentbalance", get.currentbalance);
router.get("/transactionhistory", get.transactionhistory);

module.exports = router;
