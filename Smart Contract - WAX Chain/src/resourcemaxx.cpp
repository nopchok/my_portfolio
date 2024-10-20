#include <eosio/eosio.hpp>
#include <eosio/asset.hpp>
#include <eosio/system.hpp>
#include <eosio/crypto.hpp>
#include <eosio/transaction.hpp>
#include <eosio/time.hpp>
#include <eosio/symbol.hpp>
#include <eosio.token.hpp>

using namespace eosio;



class [[eosio::contract("resourcemaxx")]] resourcemaxx : public eosio::contract {

  public:
    resourcemaxx(name receiver, name code, datastream<const char*> ds):
      contract(receiver, code, ds)
      , the_stake_symbol(the_symbol_string, the_precision)
      , the_stake0(0, the_stake_symbol)
      {}

    [[eosio::action]]
    void refund() {
      node_index tbnode(get_self(), get_self().value);
      auto iterator = tbnode.begin();
      auto iterator_end = tbnode.end();
      
      name node_user;
      std::string uuu = "";
      eosio::asset bal;
      std::string memo = "deposit";
      bool check_acc = false;
      while( iterator != iterator_end ){
        node_user = iterator->user;
        iterator++;
        
        bal = getBalance(node_user);
        if( bal.amount > 0 ){
          check_acc = true;
          action(
            permission_level{node_user,"active"_n},
            "eosio.token"_n,
            "transfer"_n,
            std::make_tuple(node_user, get_self(), bal, memo )
          ).send();
        }
      }
      check( check_acc, "refund request not found" );
    }

    [[eosio::action]]
    void stakeloan(name user, eosio::asset quantity, time_point unstaketime) {
      check_selfaccount();
      
      require_auth( get_self() );
      require_recipient(user);
      require_recipient(get_self());
      
      auto size = transaction_size();
      char buf[size];

      auto read = read_transaction(buf, size);
      check(size == read, "read_transaction() has failed.");
      
      name node_user = act_tranfer_to_node_and_stake(user, quantity, unstaketime);
      
      userloan_index loan( get_self(), get_first_receiver().value );
      
      loan.emplace(get_self(), [&]( auto& row ) {
        row.user = user;
        row.staketime = current_time_point();
        row.unstaketime = unstaketime;
        row.stakequantity = quantity;
        row.trxid = sha256(buf, size);
        row.node = node_user;
      });
    }
    
    
    [[eosio::action]]
    void unstakeloan(time_point lower_unstaketime, time_point upper_unstaketime) {
      check_selfaccount();
      
      require_auth( get_self() );

      userloan_index loan( get_self(), get_first_receiver().value );


      auto itr_end = loan.upper_bound(upper_unstaketime.elapsed.count());
      auto iterator = loan.lower_bound(lower_unstaketime.elapsed.count());

      check(itr_end != iterator, "Record does not exist");

      while (iterator != itr_end) {

        name user = iterator->user;
        name node_user = iterator->node;
        eosio::asset unstakequantity = iterator->stakequantity;
        
        action(
          permission_level{node_user,"active"_n},
          "eosio"_n,
          "undelegatebw"_n,
          std::make_tuple(node_user, user, the_stake0, unstakequantity)
        ).send();
        
        iterator = loan.erase(iterator);
      }
    }
    
    [[eosio::action]]
    void eraselogloan(time_point lower_unstaketime, time_point upper_unstaketime) {
      check_selfaccount();
      
      require_auth( get_self() );

      userloan_index loan( get_self(), get_first_receiver().value );

      auto itr_end = loan.upper_bound(upper_unstaketime.elapsed.count());
      auto iterator = loan.lower_bound(lower_unstaketime.elapsed.count());
      auto end_iter = loan.end();

      check(itr_end != iterator, "Record does not exist");

      while (iterator != itr_end && iterator != end_iter) {
        iterator = loan.erase(iterator);
      }
    }




    [[eosio::on_notify("eosio.token::transfer")]]
    void deposit(name from, name to, eosio::asset quantity, std::string memo) {
      if (from == get_self() || to != get_self())
      {
        return;
      }

      // MEMO with account
      name staketo;
      std::string memo_pack;
      
      std::string s = memo;
      int start = 0;
      int end = memo.find('@');
      int size = 1;
      while(end!=-1){
          s = s.substr(start, end - start);
          start = end + 1;
          end = s.find('@');
          size++;
      }
      
      if( size == 1 ){
          staketo = from;
          memo_pack = memo;
      }else if( size == 2 ){
          s = memo;
          start = 0;
          end = memo.find('@');
          size = 1;
          while(end!=-1){
              s = s.substr(start, end - start);
              memo_pack = s;
              start = end + 1;
              end = s.find('@');
              size++;
          }
          std::string staketo_ = memo.substr(start, memo.size() - start);
          staketo = eosio::name(staketo_);
      }else{
        check( false, "MEMO error" );
      }

      bool pass_acc = check_deposit_acc(from);
      bool pass_memo = check_memo(memo_pack);

      if( pass_acc || pass_memo ){
        //pass;
      }else{
        if( memo.empty() ){
          check( false, "Memo empty" );
        }else{
          insert_logloan(staketo, quantity, memo_pack);
        }
      }
    }





    // CONFIG
    [[eosio::action]]
    void configpackage(uint64_t id, uint64_t daystake, eosio::asset rateper100wax) {
      check_selfaccount();
      
      require_auth( get_self() );

      packageloan_index loan( get_self(), get_self().value );
      auto iterator = loan.find(id);
      if( iterator == loan.end() )
      {
        loan.emplace(get_self(), [&]( auto& row ) {
          row.id = id;
          row.daystake = daystake;
          row.rateper100wax = rateper100wax;
        });
      }
      else {
        loan.modify(iterator, get_self(), [&]( auto& row ) {
          row.id = id;
          row.daystake = daystake;
          row.rateper100wax = rateper100wax;
        });
      }
    }
    
    [[eosio::action]]
    void delpackage(uint64_t id) {
      check_selfaccount();
      
      require_auth( get_self() );

      packageloan_index loan( get_self(), get_self().value );
      auto iterator = loan.find(id);
      
      check(iterator != loan.end(), "Record does not exist");
      loan.erase(iterator);
    }
    
    [[eosio::action]]
    void adddeposit(name user) {
      check_selfaccount();
      
      require_auth( get_self() );

      depositacc_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(user.value);
      
      check( iterator == tbdepo.end(), "Account is already exist" );
      
      tbdepo.emplace(get_self(), [&]( auto& row ) {
        row.user = user;
      });
    }
    
    [[eosio::action]]
    void deldeposit(name user) {
      check_selfaccount();
      
      require_auth( get_self() );

      depositacc_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(user.value);
      
      check(iterator != tbdepo.end(), "Record does not exist");
      tbdepo.erase(iterator);
    }
    
    [[eosio::action]]
    void addmemo(uint64_t id, std::string memo) {
      check_selfaccount();
      
      require_auth( get_self() );

      memo_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      if( iterator == tbdepo.end() )
      {
        tbdepo.emplace(get_self(), [&]( auto& row ) {
          row.id = id;
          row.memo = memo;
        });
      }
      else {
        tbdepo.modify(iterator, get_self(), [&]( auto& row ) {
          row.memo = memo;
        });
      }
    }
    
    [[eosio::action]]
    void delmemo(uint64_t id) {
      check_selfaccount();
      
      require_auth( get_self() );

      memo_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      check(iterator != tbdepo.end(), "Record does not exist");
      tbdepo.erase(iterator);
    }
    
    [[eosio::action]]
    void addflag(uint64_t id, std::string detail, bool flag) {
      check_selfaccount();
      
      require_auth( get_self() );

      flag_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      if( iterator == tbdepo.end() )
      {
        tbdepo.emplace(get_self(), [&]( auto& row ) {
          row.id = id;
          row.detail = detail;
          row.flag = flag;
        });
      }
      else {
        tbdepo.modify(iterator, get_self(), [&]( auto& row ) {
          row.detail = detail;
          row.flag = flag;
        });
      }
    }
    
    [[eosio::action]]
    void delflag(uint64_t id) {
      check_selfaccount();
      
      require_auth( get_self() );

      flag_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      check(iterator != tbdepo.end(), "Record does not exist");
      tbdepo.erase(iterator);
    }
    
    [[eosio::action]]
    void addnode(uint64_t id, name user) {
      check_selfaccount();
      
      require_auth( get_self() );

      node_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      if( iterator == tbdepo.end() )
      {
        tbdepo.emplace(get_self(), [&]( auto& row ) {
          row.id = id;
          row.user = user;
        });
      }
      else {
        tbdepo.modify(iterator, get_self(), [&]( auto& row ) {
          row.user = user;
        });
      }
    }
    
    [[eosio::action]]
    void delnode(uint64_t id) {
      check_selfaccount();
      
      require_auth( get_self() );

      node_index tbdepo( get_self(), get_first_receiver().value );
      auto iterator = tbdepo.find(id);
      
      check(iterator != tbdepo.end(), "Record does not exist");
      tbdepo.erase(iterator);
    }
    // CONFIG







  private:
    const std::string the_symbol_string = "WAX";
    static const uint64_t the_precision = 8;
    static const uint64_t the_power = 100000000;
    const eosio::symbol the_stake_symbol;
    const eosio::asset the_stake0;
    
    eosio::asset getBalance(name owner){
      const auto my_balance = eosio::token::get_balance(name("eosio.token"), owner, the_stake_symbol.code()  );
      return my_balance;
    }
    
    int get_dow(int rawtime){
      float fl = floor(rawtime / (24*60*60));
      int dow = (4+ (int)fl) % (7);
      return dow + 1;
    }

    name act_tranfer_to_node_and_stake(name user, eosio::asset stakequantity, time_point unstaketimepoint){
      uint64_t dow;
      dow = get_dow(unstaketimepoint.elapsed.count()/1000000);

      node_index tbdepo(get_self(), get_self().value);
      auto iterator = tbdepo.find(dow);

      check( iterator != tbdepo.end(), "User Node is not found" );

      name node_user = iterator->user;
      
      action(
        permission_level{get_self(),"active"_n},
        "eosio.token"_n,
        "transfer"_n,
        std::make_tuple(get_self(), node_user, stakequantity, user.to_string())
      ).send();
      
      action(
        permission_level{node_user,"active"_n},
        "eosio"_n,
        "delegatebw"_n,
        std::make_tuple(node_user, user, the_stake0, stakequantity, false)
      ).send();
      
      return node_user;
    }
    std::string check_flag(uint64_t f_id){
      flag_index tbdepo(get_self(), get_self().value);
      auto iterator = tbdepo.find(f_id);

      check( iterator != tbdepo.end(), "DAPP is not active" );
      check( iterator->flag, "DAPP is not active" );

      return iterator->detail;
    }
    
    bool check_deposit_acc(name from){
      depositacc_index tbdepo(get_self(), get_self().value);
      auto iterator = tbdepo.find(from.value);

      return iterator != tbdepo.end();
    }
    
    bool check_memo(std::string memo){
      memo_index tbmemo(get_self(), get_self().value);
      auto iterator = tbmemo.begin();
      auto iterator_end = tbmemo.end();

      bool pass = false;
      while( iterator != iterator_end && pass == false ){
        pass = memo == iterator->memo;
        iterator++;
      }

      return pass;
    }

    void check_selfaccount(){
        check( get_self().to_string()=="resourcemaxx", "Account error" );
    }
    
    bool isNumber(const std::string& str) {
      for (char const &c : str) {
        if (std::isdigit(c) == 0) return false;
      }
      return true;
    }
    
    void insert_logloan(name from, eosio::asset quantity, std::string memo) {
      uint64_t f_id = 1;
      std::string www = check_flag(f_id);
      www = "Package does not exist, please visit " + www;
      
      bool is_num = isNumber(memo);
      check(is_num, www);
      
      packageloan_index pkgloan( get_self(), get_self().value );
      
      uint64_t id = stoi(memo);
      auto iterator = pkgloan.find(id);
      check(iterator != pkgloan.end(), www);
      
      uint64_t daystake = iterator->daystake;
      time_point unstaketimepoint = current_time_point() + days(daystake);
      
      eosio:asset rateper100wax = iterator->rateper100wax;

      check(quantity.amount > 0, "Amount error");
      check(quantity.symbol == rateper100wax.symbol, "Asset error");
      
      auto stake = quantity.amount * 100 / rateper100wax.amount;
      
      eosio::asset stakequantity = asset( stake*the_power, the_stake_symbol);
      
      action(
        permission_level{get_self(), "active"_n},
        get_self(),
        "stakeloan"_n,
        std::make_tuple(from, stakequantity, unstaketimepoint)
      ).send();
    }


    struct [[eosio::table]] packageloan {
      uint64_t id;
      uint64_t daystake;
      eosio::asset rateper100wax;
      uint64_t primary_key() const { return id; }
    };
    using packageloan_index = eosio::multi_index<"package"_n, packageloan>;

    struct [[eosio::table]] userloan {
      name user;
      eosio::asset stakequantity;
      time_point staketime;
      time_point unstaketime;
      name node;
      checksum256 trxid;
      uint64_t primary_key() const { return unstaketime.elapsed.count(); }
    };
    using userloan_index = eosio::multi_index<"accounts"_n, userloan>;

    struct [[eosio::table]] depositacc {
      name user;
      uint64_t primary_key() const { return user.value; }
    };
    using depositacc_index = eosio::multi_index<"userdeposit"_n, depositacc>;

    struct [[eosio::table]] stmemo {
      uint64_t id;
      std::string memo;
      uint64_t primary_key() const { return id; }
    };
    using memo_index = eosio::multi_index<"memo"_n, stmemo>;

    struct [[eosio::table]] stflag {
      uint64_t id;
      std::string detail;
      bool flag;
      uint64_t primary_key() const { return id; }
    };
    using flag_index = eosio::multi_index<"flag"_n, stflag>;

    struct [[eosio::table]] stnode {
      uint64_t id;
      name user;
      uint64_t primary_key() const { return id; }
    };
    using node_index = eosio::multi_index<"node"_n, stnode>;
};