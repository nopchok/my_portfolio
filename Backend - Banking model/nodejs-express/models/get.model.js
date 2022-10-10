const errMsg = require("../configs/errorMessage");

const db = require("../configs/databases");
const common = require("../models/common");

const methods = {
  CurrentBalance(param) {
    return new Promise(async (resolve, reject) => {
      try {
        const { id } = param;

        const CurrentBalance = await common.getCurrentBalance(db, id);

        resolve({ success: true, data: CurrentBalance });
      } catch (error) {
        reject(error);
      }
    });
  },
  TransactionHistory(param) {
    return new Promise(async (resolve, reject) => {
      try {
        const { id } = param;

        if (await common.count(db, "BankAccount", "Id", id) == 0) throw new Error(errMsg.bankAccountNotFound);

        const TransactionHistory = await db.select().from("TransactionHistory").where("BankAccountId", id);

        resolve({ success: true, data: TransactionHistory[0] });
      } catch (error) {
        reject(error);
      }
    });
  },
};

module.exports = { ...methods };
