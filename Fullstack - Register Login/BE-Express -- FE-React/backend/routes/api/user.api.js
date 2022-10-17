const router = require("express").Router();
const user = require("../../controllers/user.controller");

router.post("/register", user.validate_register, user.register);
router.post("/login", user.validate_login, user.login);


module.exports = router;
