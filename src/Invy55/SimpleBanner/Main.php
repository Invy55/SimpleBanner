<?php
/*
8888888                            888888888  888888888  
  888                              888        888        
  888                              888        888        
  888   88888b.  888  888 888  888 8888888b.  8888888b.  
  888   888 "88b 888  888 888  888      "Y88b      "Y88b 
  888   888  888 Y88  88P 888  888        888        888 
  888   888  888  Y8bd8P  Y88b 888 Y88b  d88P Y88b  d88P 
8888888 888  888   Y88P    "Y88888  "Y8888P"   "Y8888P"  
                               888                       
                          Y8b d88P                       
                           "Y88P"
----- This project is under the GNU Affero General Public License v3.0 -----                       
*/
declare(strict_types=1);
namespace Invy55\SimpleBanner;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use jojoe77777\FormAPI\SimpleForm; //Form api
use pocketmine\nbt\JsonNbtParser;
class Main extends PluginBase implements Listener{
    public function onEnable() {
        $this->patterns = ['gra', 'gru', 'bri', 'hh','hhb','vh','vhr','ts','bs','ls','rs','ld','rud','lud','rd','cr','dls','drs','sc','cs','ms','tl','bl','tr','br','tt','bt','mr','mc','bts','tts','ss','bo','cbo','flo','cre','sku','moj'];
        $this->saveResource("config.yml");
        $this->saveResource("players-data.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->playerdata = new Config($this->getDataFolder() . "players-data.yml", Config::YAML);
        if($this->config->get("banner-number") == FALSE or !is_numeric($this->config->get("banner-number")) or $this->config->get("banner-number") > 16 or $this->config->get("banner-number") < 1){
            $this->config->set("banner-number", 16);
            $this->config->save();
        }
        if($this->config->get("banner-timeout") == FALSE or !is_numeric($this->config->get("banner-timeout")) or $this->config->get("banner-timeout") < 0){
            $this->config->set("banner-timeout", 0);
            $this->config->save();
        }
        if($this->config->get("banner-language") == FALSE or !file_exists($this->getDataFolder() . strtolower($this->config->get("banner-language")) . ".yml")){
            $this->config->set("banner-language", "english");
            $this->getServer()->getLogger()->info("Language ".$this->config->get("banner-language")." not found (file name must be all low caps), using English.");
        }
        if($this->config->get("banner-language") == "english" and !file_exists($this->getDataFolder() . strtolower($this->config->get("banner-language")) . ".yml")){
            file_put_contents($this->getDataFolder() . strtolower($this->config->get("banner-language")) . ".yml", "");
        }
        $this->translation = new Config($this->getDataFolder() . strtolower($this->config->get("banner-language")) . ".yml", Config::YAML);
        $this->defaultValues = ['Gradient_top_to_bottom'=>'Gradient top to bottom', 'Gradient_bottom_to_top'=>'Gradient bottom to top', 'Bricks'=>'Bricks', 'Top_half_rectangle'=>'Top half rectangle',
        'Bottom_half_rectangle'=>'Bottom half rectangle', 'Left_half_rectangle'=>'Left half rectangle', 'Right_half_rectangle'=>'Right half rectangle',
        'Top_small_rectangle'=>'Top small rectangle', 'Bottom_small_rectangle'=>'Bottom small rectangle', 'Left_small_rectangle'=>'Left small rectangle',
        'Right_small_rectangle'=>'Right small rectangle', 'Top_left_triangle'=>'Top left triangle', 'Top_right_triangle'=>'Top right triangle',
        'Bottom_left_triangle'=>'Bottom left triangle', 'Bottom_right_triangle'=>'Bottom right triangle', 'Big_X'=>'Big §lX', 'Diagonal1'=>'Diagonal §l/',
        'Diagonal2'=>'Diagonal §l\\', 'Cross'=>'Cross §l+', 'Centered_vertical_line'=>'Centered vertical line', 'Centered_horizontal_line'=>'Centered horizontal line',
        'Top_left_square'=>'Top left square', 'Top_right_square'=>'Top right square', 'Bottom_left_square'=>'Bottom left square', 'Bottom_right_square'=>'Bottom right square',
        'Top_triangle'=>'Top triangle', 'Bottom_triangle'=>'Bottom triangle', 'Centered_rhombus'=>'Centered rhombus', 'Centered_circle'=>'Centered "circle"',
        'Bottom_spikes'=>'Bottom spikes', 'Top_spikes'=>'Top spikes', '4_horizontal_lines'=>'4 horizontal lines', 'Frame'=>'Frame', 'Spiky_frame'=>'Spiky frame',
        'Centered_flower'=>'Centered flower', 'Creeper_head'=>'Creeper head', 'Centered_skull'=>'Centered skull', 'Mojang_logo'=>'Mojang logo',
        'BLACK'=>'BLACK', 'DARK_GREEN'=>'DARK_GREEN', 'DARK_AQUA'=>'DARK_AQUA', 'DARK_PURPLE'=>'DARK_PURPLE', 'ORANGE'=>'ORANGE', 'GRAY'=>'GRAY', 'DARK_GRAY'=>'DARK_GRAY', 'BLUE'=>'BLUE', 'GREEN'=>'GREEN', 'AQUA'=>'AQUA', 'RED'=>'RED', 'LIGHT_PURPLE'=>'LIGHT_PURPLE', 'YELLOW'=>'YELLOW', 'WHITE'=>'WHITE',
        'Please_wait'=>'§7Please wait §l{x} seconds', 
        'Choose_background'=>'§4Please select a background color:§r §0black§r, §2dark_green§r, §3dark_aqua§r, §5dark_purple§r, §6orange§r, §7gray§r, §8dark_gray§r, §9blue§r, §agreen§r, §baqua§r, §cred§r, §dlight_purple§r, §eyellow§r, §fwhite',
        'Color_not_found'=>'§4Color {x} not found, avaiable colors:§r §0black§r, §2dark_green§r, §3dark_aqua§r, §5dark_purple§r, §6orange§r, §7gray§r, §8dark_gray§r, §9blue§r, §agreen§r, §baqua§r, §cred§r, §dlight_purple§r, §eyellow§r, §fwhite',
        'Creating_banner'=>'Creating a {x} banner', 'Choose_color'=>'Choose a color for the pattern: §l{x}', 'Select_pattern'=>'Select a pattern', 'Done'=>'§k|-| §rDone',
        'Pattern_name'=>'Edited {x} pattern', 'Finished_message'=>'§aOk finished! Generated banner name: §r{x}',
        ];
        $this->items = [self::getTranslation('Gradient top to bottom'), self::getTranslation('Gradient bottom to top'), self::getTranslation('Bricks'), self::getTranslation('Top half rectangle'), self::getTranslation('Bottom half rectangle'), self::getTranslation('Left half rectangle'), self::getTranslation('Right half rectangle'), self::getTranslation('Top small rectangle'), self::getTranslation('Bottom small rectangle'), self::getTranslation('Left small rectangle'), self::getTranslation('Right small rectangle'), self::getTranslation('Top left triangle'), self::getTranslation('Top right triangle'), self::getTranslation('Bottom left triangle'), self::getTranslation('Bottom right triangle'), self::getTranslation('Big_X'), self::getTranslation('Diagonal1'), self::getTranslation('Diagonal2'), self::getTranslation('Cross'), self::getTranslation('Centered vertical line'), self::getTranslation('Centered horizontal line'), self::getTranslation('Top left square'), self::getTranslation('Top right square'), self::getTranslation('Bottom left square'), self::getTranslation('Bottom right square'), self::getTranslation('Top triangle'), self::getTranslation('Bottom triangle'), self::getTranslation('Centered rhombus'), self::getTranslation('Centered circle'), self::getTranslation('Bottom spikes'), self::getTranslation('Top spikes'), self::getTranslation('4 horizontal lines'), self::getTranslation('Frame'), self::getTranslation('Spiky frame'), self::getTranslation('Centered flower'), self::getTranslation('Creeper head'), self::getTranslation('Centered skull'), self::getTranslation('Mojang logo')];
        $this->colortags = [self::getTranslation('BLACK')=>'0', self::getTranslation('DARK_GREEN')=>'2', self::getTranslation('DARK_AQUA')=>'3', self::getTranslation('DARK_PURPLE')=>'5', self::getTranslation('ORANGE')=>'6', self::getTranslation('GRAY')=>'7', self::getTranslation('DARK_GRAY')=>'8', self::getTranslation('BLUE')=>'9', self::getTranslation('GREEN')=>'a', self::getTranslation('AQUA')=>'b', self::getTranslation('RED')=>'c', self::getTranslation('LIGHT_PURPLE')=>'d', self::getTranslation('YELLOW')=>'e', self::getTranslation('WHITE')=>'f'];
        $this->colors = [self::getTranslation('BLACK'), self::getTranslation('DARK_GREEN'), self::getTranslation('DARK_AQUA'), self::getTranslation('DARK_PURPLE'), self::getTranslation('ORANGE'), self::getTranslation('GRAY'), self::getTranslation('DARK_GRAY'), self::getTranslation('BLUE'), self::getTranslation('GREEN'), self::getTranslation('AQUA'), self::getTranslation('RED'), self::getTranslation('LIGHT_PURPLE'), self::getTranslation('YELLOW'), self::getTranslation('WHITE')];
        $this->bannerc = [self::getTranslation('BLACK')=>'0', self::getTranslation('DARK_GREEN')=>'2', self::getTranslation('DARK_AQUA')=>'6', self::getTranslation('DARK_PURPLE')=>'5', self::getTranslation('ORANGE')=>'14', self::getTranslation('GRAY')=>'7', self::getTranslation('DARK_GRAY')=>'8', self::getTranslation('BLUE')=>'4', self::getTranslation('GREEN')=>'10', self::getTranslation('AQUA')=>'12', self::getTranslation('RED')=>'1', self::getTranslation('LIGHT_PURPLE')=>'9', self::getTranslation('YELLOW')=>'11', self::getTranslation('WHITE')=>'15'];

    }

    public function getTranslation(string $strname){
        $strname = str_replace(" ", "_", $strname);
        if($this->translation->get($strname) == FALSE){
            if(@$this->defaultValues[$strname] != NULL){
                return $this->defaultValues[$strname];
            }else{
                return "Unknown translation";
            }
        }else{
            return $this->translation->get($strname);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		$player = $sender->getName();
		switch($command->getName()){
            case "banner":
                $plconfig = $this->playerdata->get($player);
                if($plconfig == FALSE){
                    $timeout = TRUE;
                }elseif(!is_numeric($plconfig)){
                    $timeout = TRUE;
                }elseif($plconfig+$this->config->get("banner-timeout") > microtime(TRUE)){
                    $timeout = FALSE;
                }else{
                    $timeout = TRUE;
                }
                if($sender->hasPermission("simplebanner.command.notimeout") or $timeout){
                    if(isset($args[0])){
                        if(!in_array(strtoupper($args[0]), $this->colors)){
                            $sender->sendMessage(str_replace("{x}", $args[0], self::getTranslation("Color_not_found")).'§r');
                        }else{ 
                            $this->$player =  new \stdClass(); 
                            $this->layer($sender, strtolower($args[0]));
                        }
                    }else{
                        $sender->sendMessage(self::getTranslation("Choose_background").'§r');
                    }
                }else{
                    $towait = $plconfig+$this->config->get("banner-timeout");
                    $sender->sendMessage(str_replace("{x}", strval(intval($towait-microtime(TRUE))), self::getTranslation("Please_wait")).'§r');
                }
			default:
				return false;
        }
        
	}
    public function layer($player, $color, $all = false){
       $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            switch ($result) {
                case 0:
                default:
                    $playern = $player->getName();
                    if($this->$playern->all === false){
                        $selected = $result;
                    }elseif($result == 0){
                        $playern = $player->getName();
                        $to_text = '§'.$this->colortags[strtoupper($this->$playern->color)].str_replace("{x}", $this->$playern->color, self::getTranslation("Pattern_name"));
                        $player->sendMessage(str_replace("{x}", $to_text, self::getTranslation("Finished_message")));
                        $item = Item::fromString("minecraft:banner:".$this->bannerc[strtoupper($this->$playern->color)]);
                        $item->setCount(intval($this->config->get("banner-number")));
                        $item->setNamedTag(JsonNbtParser::parseJSON("{display:{Name:".$to_text."},BlockEntityTag:{Base:".$this->bannerc[strtoupper($this->$playern->color)].",Patterns:[".substr($this->$playern->all, 0, -1)."]}}"));
                        $player->getInventory()->addItem($item);
                        $this->$playern->color = null;
                        $this->$playern->all = null;
                        $this->$playern->pattern = null;
                        $this->playerdata->set($playern, microtime(true));
                        $this->playerdata->save();
                        return;
                    }else{
                        $selected = $result-1;
                    }
                    $this->color($player, $this->$playern->color, $this->$playern->all, $selected);
                    return;
            }
        });
        $colortag = '§'.$this->colortags[strtoupper($color)];
        $form->setTitle(str_replace("{x}", $colortag.$color.'§r', self::getTranslation("Creating_banner")));
        $form->setContent(self::getTranslation("Select_pattern"));
        if($all !== false) $form->addButton(self::getTranslation("Done"));
        foreach($this->items as $item){
            $form->addButton($item);
        }
        $playern = $player->getName();
        $this->$playern->color = $color;
        $this->$playern->all = $all;
        $form->sendToPlayer($player);
    }
    public function color($player, $color, $all, $pattern){
       $form = new SimpleForm(function (Player $player, $data = null ) {
            $result = $data;
            if ($result === null) {
                return true;
            }
            $playern = $player->getName();
            $this->$playern->all .=  '{Pattern:' . $this->patterns[$this->$playern->pattern] . ',Color:' . $this->bannerc[$this->colors[$result]].'},';
            $this->layer($player, $this->$playern->color, $this->$playern->all);
            return;
        });
        $colortag = '§'.$this->colortags[strtoupper($color)];
        $form->setTitle(str_replace("{x}", $colortag.$color.'§r', self::getTranslation("Creating_banner")));
        $form->setContent(str_replace("{x}", $this->items[$pattern], self::getTranslation("Choose_color")));
        foreach($this->colors as $item){
            $form->addButton('§'.$this->colortags[$item] . ucfirst(strtolower(str_replace('_', ' ', $item))));
        }
        $playern = $player->getName();
        $this->$playern->pattern = $pattern;
        $form->sendToPlayer($player);
    }
}
