const router = require("express").Router();

const auth = require('../../middlewares/auth');


router.use("/user", require("./user.api"));

router.get("/test", auth.validate_auth, auth.isAuthorized, (req, res) => {
    try {
        const result = [1, 2, 3, 4];
        res.success(result);
    } catch (error) {
        res.error(error);
    }
});


module.exports = router;
