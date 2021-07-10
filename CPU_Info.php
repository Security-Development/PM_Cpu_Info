<?php
/**
 * @name CPU_Info
 * @author Security-Development
 * @main CPU_Info\CPU_Info
 * @version 0.1.0
 * @api 3.13.0
 */

 namespace CPU_Info;
 use pocketmine\{
   plugin\PluginBase,
   event\Listener
 };
 class CPU_Info extends PluginBase implements Listener
 {
   public $cpu_info;
   function onEnable() :void
   {
     $server = \pocketmine\Server::getInstance();
     $server->getPluginManager()->registerEvents($this, $this);

     $a = "grep ^'model name' /proc/cpuinfo";
     $b = "top -b -n1 | grep -Po '[0-9.]+ id' | awk '{print 100-$1}'";

     $this->cpu_info['cpu'] = function (\pocketmine\Player $player) use($server, $a ,$b): void
     {
       $msg = "§a[ CPU 정보 ]\nCPU 모델 : " . system($a)."\nCPU 사용률 : ". system($b) . "%";
       $player->sendMessage($msg);
     };

     $server->getCommandMap()->registerAll('cpu', [
       new class($this) extends \pocketmine\command\Command
       {
         public function __construct($plugin)
         {
           $this->plugin = $plugin;

           parent::__construct (
             'cpu',
             'cpu info'
           );
           $this->setPermission('op');
         }

         function execute(\pocketmine\command\CommandSender $sender, $commandLabel, array $args) :bool
         {
           $this->plugin->cpu_info['cpu']($sender);
           return false;
         }
       }
     ]);
   }

   function onJoin(\pocketmine\event\player\PlayerJoinEvent $event): void
   {
     $player = $event->getPlayer();
     $this->cpu_info['cpu']($player);
   }
 }
 ?>
