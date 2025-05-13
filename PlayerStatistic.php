<?php
class PlayerStatistic
{
   // Instance attributes
   private $name         = array('FIRST'=>"", 'LAST'=>null);
   private $diff_score = 0;
   private $exec_score      = 0;
   private $fin_score     = 0;

   // Operations

   // name() prototypes:
   //   string name()                          returns name in "Last, First" format.
   //                                          If no first name assigned, then return in "Last" format.
   //
   //   void name(string $value)               set object's $name attribute in "Last, First"
   //                                          or "Last" format.
   //
   //   void name(array $value)                set object's $name attribute in [first, last] format
   //
   //   void name(string $first, string $last) set object's $name attribute
   function name()
   {
     // string name()
     if( func_num_args() == 0 )
     {
       if( empty($this->name['FIRST']) ) return $this->name['LAST'];
       else                              return $this->name['LAST'].', '.$this->name['FIRST'];
     }

     // void name($value)
     else if( func_num_args() == 1 )
     {
       $value = func_get_arg(0);

       if( is_string($value) )
       {
         $value = explode(',', $value); // convert string to array

         if ( count($value) >= 2 ) $this->name['FIRST'] = htmlspecialchars(trim($value[1]));
         else                      $this->name['FIRST'] = '';

         $this->name['LAST']  = htmlspecialchars(trim($value[0]));
       }

       else if( is_array ($value) )
       {
         if ( count($value) >= 2 ) $this->name['LAST'] = htmlspecialchars(trim($value[1]));
         else                      $this->name['LAST'] = '';

         $this->name['FIRST']  = htmlspecialchars(trim($value[0]));
       }
     }

     // void name($first_name, $last_name)
     else if( func_num_args() == 2 )
     {
         $this->name['FIRST'] = htmlspecialchars(trim(func_get_arg(0)));
         $this->name['LAST']  = htmlspecialchars(trim(func_get_arg(1)));
     }

     return $this;
   }








   // playingTime() prototypes:
   //   string playingTime()                          returns playing time in "minutes:seconds" format.
   //
   //   void playingTime(string $value)               set object's $playingTime attribute
   //                                                 in "minutes:seconds" format.
   //
   //   void playingTime(array $value)                set object's $playingTime attribute
   //                                                 in [minutes, seconds] format
   //
   //   void playingTime(int $minutes, int $seconds)  set object's $playingTime attribute
   



   // pointsScored() prototypes:
   //   int pointsScored()               returns the number of points scored.
   //
   //   void pointsScored(int $value)    set object's $pointsScored attribute
   function diff_score()
   {
     // int pointsScored()
     if( func_num_args() == 0 )
     {
       return $this->diff_score;
     }

     // void pointsScored($value)
     else if( func_num_args() == 1 )
     {
       $this->diff_score = (int)func_get_arg(0);
     }

     return $this;
   }








   // exec_score() prototypes:
   //   int exec_score()               returns the number of scoring exec_score.
   //
   //   void exec_score(int $value)    set object's $exec_score attribute
   function exec_score()
   {
     // int exec_score()
     if( func_num_args() == 0 )
     {
       return $this->exec_score;
     }

     // void exec_score($value)
     else if( func_num_args() == 1 )
     {
       $this->exec_score = (int)func_get_arg(0);
     }

     return $this;
   }








   // fin_score() prototypes:
   //   int fin_score()               returns the number of fin_score taken.
   //
   //   void fin_score(int $value)    set object's $fin_score attribute
   function fin_score()
   {
     // int fin_score()
     if( func_num_args() == 0 )
     {
       return $this->fin_score;
     }

     // void fin_score($value)
     else if( func_num_args() == 1 )
     {
       $this->fin_score = (int)func_get_arg(0);
     }

     return $this;
   }








   function __construct($name="", $diff_score=0, $exec_score=0, $fin_score=0)
   {
     // if $name contains at least one tab character, assume all attributes are provided in
     // a tab separated list.  Otherwise assume $name is just the player's name.
     if( is_string($name) && strpos($name, "\t") !== false) // Note, can't check for "true" because strpos() only returns the boolean value "false", never "true"
     {
       // assign each argument a value from the tab delineated string respecting relative positions
       list($name,  $diff_score, $exec_score, $fin_score) = explode("\t", $name);
     }

     // delegate setting attributes so validation logic is applied
     $this->name($name);
     $this->diff_score($diff_score);
     $this->exec_score($exec_score);
     $this->fin_score($fin_score);
   }








   function __toString()
   {
     return (var_export($this, true));
   }








   // Returns a tab separated value (TSV) string containing the contents of all instance attributes
   function toTSV()
   {
       return implode("\t", [$this->name(), $this->pointsScored(), $this->exec_score(), $this->fin_score()]);
   }


   // Sets instance attributes to the contents of a string containing ordered, tab separated values
   function fromTSV(string $tsvString)
   {
     // assign each argument a value from the tab delineated string respecting relative positions
     list($name, $diff_score, $exec_score, $fin_score) = explode("\t", $tsvString);
     $this->name($name);
     $this->diff_score($diff_score);
     $this->exec_score($exec_score);
     $this->fin_score($fin_score);
   }
} // end class PlayerStatistic

?>
