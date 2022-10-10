const router = require("express").Router();
const create = require("../../controllers/create.controller");

router.post("/newaccount", create.validate_newaccount, create.newaccount);
router.post("/transferamount", create.validate_transferamount, create.transferamount);

module.exports = router;
