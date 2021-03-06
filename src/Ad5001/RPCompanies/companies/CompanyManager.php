<?php


namespace Ad5001\RPCompanies;



use pocketmine\Server;


use pocketmine\Player;



use Ad5001\RPCompanies\Main;







class CompanyManager {


    protected static $companies = [];


    /*
    Register all companies
    @param     $main    Main
    */
    public static function registerAll(Main $main) {
		$this->db = new \SQLite3($main->getDataFolder() . "companies.db");
        foreach($this->db->query("SELECT name FROM companies")->fetchArray() as $arr) {
            self::$companies[$arr["name"]] = new Company($arr["name"]);
        }
    }


    /*
    Register a company
    @param     $company    Company
    */
    public function register(Company $company) {
        self::$companies[$company->getName()] = $company;
    }


    public static function getCompanies() : array {
		return self::$companies;
	}
	
	
	public function getCompanyByName(string $name) {
		return isset(self::$companies[$name]) ? self::$companies[$name] : null;
	}
	
	
	public static function getCompanyOfPlayer(Player $player) {
		foreach(self::getCompanies() as $c) {
			if(in_array($player->getName(), $c->getEmployes())) {
				return $c;
			}
		}
		return null;
	}
	
	
	public static function getCompanyFromPos(Position $pos) {
		if($pos->getLevel()->getName() == Main::$instance->getConfig()->get("RPLevel")) {
			$chunk = $pos->getLevel()->getChunk($pos->x, $pos->z);
			foreach(self::$companies as $name => $c) {
				foreach($c->getChunks() as $chunkid => $posarray) {
					if($chunk->x == $posarray["x"] && $chunk->z == $posarray["z"]) {
						return $c;
					}
				}
			}
		}
		return null;
	}
	
	
	
	public static function getCompanyFromPlayer(Player $player) {
		foreach(self::$companies as $name => $c) {
			foreach($c->getChunks() as $chunkid => $posarray) {
				if($player->chunk->x == $posarray["x"] && $player->chunk->z == $posarray["z"]) {
					return $c;
				}
			}
		}
	}


    /*
    Deletes a comapny
    @param     $name    string
    */
    public function deleteCompany(string $name) {
        if(!isset(self::$companies[$name])) return false;
       self::$companies[$name]->delete();
       unset(self::$companies[$name]);
    }




}