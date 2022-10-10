const router = require("express").Router();

router.use("/create", require("./create.api"));
router.use("/get", require("./get.api"));

module.exports = router;
