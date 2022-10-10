const router = require("express").Router();
const create = require("../../controllers/create.controller");

router.get("/newaccount", create.newaccount);
router.get("/transferamount", create.transferamount);

module.exports = router;
