const errMsg = require("../configs/errorMessage");

const db = require("../configs/databases");
const common = require("../models/common");

const methods = {
  newaccount(param) {
    return new Promise(async (resolve, reject) => {
      const trx = await db.transaction();
      try {
        let q;
        const { name, initial_deposit } = param;

        const CustomerId = await common.getCustomerId(trx, name);

        const new_id = await trx
          .insert({
            CurrentBalance: initial_deposit,
            CustomerId: CustomerId,
          })
          .returning("Id")
          .into("BankAccount");

        await trx.commit();
        resolve({ success: true, data: { new_id: new_id[0].Id } });
      } catch (error) {
        await trx.rollback();
        reject(error);
      }
    });
  },
  transferamount(param) {
    return new Promise(async (resolve, reject) => {
      const trx = await db.transaction();
      try {
        const { from, to, amount, transaction_type, relate_transaction_type } = param;

        if (await common.count(trx, "BankAccount", "Id", from) == 0) throw new Error(errMsg.bankAccountNotFound);
        if (await common.count(trx, "BankAccount", "Id", to) == 0) throw new Error(errMsg.bankAccountNotFound);

        const CurrentBalance = await common.getCurrentBalance(trx, from);
        if (CurrentBalance < amount) throw new Error(errMsg.balanceNotEnough);

        let transaction_type_id = await common.getTransactionTypeId(trx, transaction_type);
        let relate_transaction_type_id = await common.getTransactionTypeId(trx, relate_transaction_type);

        await common.updateCurrentBalance(trx, from, -amount);
        await common.updateCurrentBalance(trx, to, amount);

        await trx
          .insert({
            TransactionTypeId: relate_transaction_type_id,
            BankAccountId: to,
            Amount: amount,
            RelateBankAccountId: to,
          })
          .into("TransactionHistory");

        const transaction_id = await trx
          .insert({
            TransactionTypeId: transaction_type_id,
            BankAccountId: from,
            Amount: -amount,
            RelateBankAccountId: to,
          })
          .returning("Id")
          .into("TransactionHistory");

        await trx.commit();
        resolve({ success: true, data: { transaction_id: transaction_id[0].Id } });
      } catch (error) {
        await trx.rollback();
        reject(error);
      }
    });
  },
};

module.exports = { ...methods };
