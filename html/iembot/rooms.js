/*
select '["'|| lower(id) || 'chat","' || name || '"],' from stations WHERE network = 'WFO' ORDER by id
*/

Ext.namespace('chatdata');

chatdata.rooms = [
 ['botstalk','All Bots Talk'],
 ["nhcchat","National Hurricane Center (NHC)"],
 ["spcchat","Storm Prediction Center (SPC)"],
 ["wpcchat","Weather Prediction Center (WPC)"],
 ["wbcchat", "Weather Bureau Central / NWSHQ (WBC)"],
 ["abqchat","Albuquerque"],
 ["abrchat","Aberdeen"],
 ["afcchat","Anchorage"],
 ["afgchat","Fairbanks"],
 ["ajkchat","Juneau"],
 ["akqchat","Wakefield"],
 ["alychat","Albany"],
 ["amachat","Amarillo"],
 ["apxchat","Gaylord"],
 ["arxchat","La Crosse"],
 ["bgmchat","Binghamton"],
 ["bischat","Bismarck"],
 ["bmxchat","Birmingham"],
 ["boichat","Boise"],
 ["bouchat","Denver"],
 ["boxchat","Boston/Taunton"],
 ["brochat","Brownsville"],
 ["btvchat","Burlington"],
 ["bufchat","Buffalo"],
 ["byzchat","Billings"],
 ["caechat","Columbia"],
 ["carchat","Caribou"],
 ["chschat","Charleston"],
 ["clechat","Cleveland"],
 ["crpchat","Corpus Christi"],
 ["ctpchat","State College"],
 ["cyschat","Cheyenne"],
 ["ddcchat","Dodge City"],
 ["dlhchat","Duluth"],
 ["dmxchat","Des Moines"],
 ["dtxchat","Detroit"],
 ["dvnchat","Quad Cities Ia"],
 ["eaxchat","Kansas City/Pleasant Hill"],
 ["ekachat","Eureka"],
 ["epzchat","El Paso Tx/Santa Teresa"],
 ["ewxchat","Austin/San Antonio"],
 ["ffcchat","Peachtree City"],
 ["fgfchat","Grand Forks"],
 ["fgzchat","Flagstaff"],
 ["fsdchat","Sioux Falls"],
 ["fwdchat","Dallas/Fort Worth"],
 ["ggwchat","Glasgow"],
 ["gidchat","Hastings"],
 ["gjtchat","Grand Junction"],
 ["gldchat","Goodland"],
 ["grbchat","Green Bay"],
 ["grrchat","Grand Rapids"],
 ["gspchat","Greenville/Spartanburg"],
 ["gumchat","Guam"],
 ["gyxchat","Gray"],
 ["hawaii", "Hawaii"],
 ["hfochat","Honolulu"],
 ["hgxchat","Houston/Galveston"],
 ["hnxchat","San Joaquin Valley/Hanford"],
 ["hunchat","Huntsville"],
 ["ictchat","Wichita"],
 ["ilmchat","Wilmington"],
 ["ilnchat","Wilmington"],
 ["ilxchat","Lincoln"],
 ["indchat","Indianapolis"],
 ["iwxchat","Northern Indiana"],
 ["janchat","Jackson"],
 ["jaxchat","Jacksonville"],
 ["jklchat","Jackson"],
 ["jsjchat","San Juan"],
 ["keychat","Key West"],
 ["lbfchat","North Platte"],
 ["lchchat","Lake Charles"],
 ["lixchat","New Orleans"],
 ["lknchat","Elko"],
 ["lmkchat","Louisville"],
 ["lotchat","Chicago"],
 ["loxchat","Los Angeles/Oxnard"],
 ["lsxchat","St Louis"],
 ["lubchat","Lubbock"],
 ["lwxchat","Baltimore Md/ Washington Dc"],
 ["lzkchat","Little Rock"],
 ["mafchat","Midland/Odessa"],
 ["megchat","Memphis"],
 ["mflchat","Miami"],
 ["mfrchat","Medford"],
 ["mhxchat","Newport/Morehead City"],
 ['michiganwxalerts', "Michigan Weather Alerts"],
 ["mkxchat","Milwaukee/Sullivan"],
 ["mlbchat","Melbourne"],
 ["mobchat","Mobile"],
 ["mpxchat","Twin Cities/Chanhassen"],
 ["mqtchat","Marquette"],
 ["mrxchat","Morristown"],
 ["msochat","Missoula"],
 ["mtrchat","San Francisco"],
 ["oaxchat","Omaha/Valley"],
 ["ohxchat","Nashville"],
 ["okxchat","New York"],
 ["otxchat","Spokane"],
 ["ounchat","Norman"],
 ["pahchat","Paducah"],
 ["pbzchat","Pittsburgh"],
 ["pdtchat","Pendleton"],
 ["phichat","Mount Holly"],
 ["pihchat","Pocatello/Idaho Falls"],
 ["pqrchat","Portland"],
 ["psrchat","Phoenix"],
 ["pubchat","Pueblo"],
 ["rahchat","Raleigh"],
 ["revchat","Reno"],
 ["riwchat","Riverton"],
 ["rlxchat","Charleston"],
 ["rnkchat","Blacksburg"],
 ["sewchat","Seattle"],
 ["sgfchat","Springfield"],
 ["sgxchat","San Diego"],
 ["shvchat","Shreveport"],
 ["sjtchat","San Angelo"],
 ["sjuchat","San Juan"],
 ["slcchat","Salt Lake City"],
 ["stochat","Sacramento"],
 ["taechat","Tallahassee"],
 ["tbwchat","Tampa Bay Area/Ruskin"],
 ["tfxchat","Great Falls"],
 ["topchat","Topeka"],
 ["tsachat","Tulsa"],
 ["twcchat","Tucson"],
 ["unrchat","Rapid City"],
 ["vefchat","Las Vegas"],
 ["alrchat","Atlanta RFC"],
 ["fwrchat","West Gulf RFC"],
 ["krfchat","Missouri River Basin RFC"],
 ["msrchat","North Central RFC"],
 ["ornchat","Lower Mississippi RFC"],
 ["pacrchat","Alaska - Pacific RFC"],
 ["ptrchat","Northwest RFC"],
 ["rhachat","Mid Atlantic RFC"],
 ["rsachat","California - Nevada RFC"],
 ["strchat","Colorado RFC"],
 ["tarchat","Northeast RFC"],
 ["tirchat","Ohio RFC"],
 ["tuachat","Arkansas Red River RFC"],
 ["pancchat","Anchorage"],
 ["zabchat","Albuquerque"],
 ["zauchat","Chicago"],
 ["zbwchat","Boston"],
 ["zdcchat","Washington DC"],
 ["zdvchat","Denver"],
 ["zfwchat","Fort Worth"],
 ["zhnchat","Honolulu CWSU"],
 ["zhuchat","Houston"],
 ["zidchat","Indianapolis"],
 ["zjxchat","Jacksonville"],
 ["zkcchat","Kansas City"],
 ["zlachat","Los Angeles"],
 ["zlcchat","Salt Lake City"],
 ["zmachat","Miami"],
 ["zmechat","Memphis"],
 ["zmpchat","Minneapolis"],
 ["znychat","New York"],
 ["zoachat","Oakland"],
 ["zobchat","Cleveland"],
 ["zsechat","Seattle"],
 ["ztlchat","Atlanta"],
 ["n90", "N90 TRACON New York City"],
 ["potomac_tracon", "Potomac TRACON Washington DC"],
 ["phl", "PHL TRACON Philadephia"]
];
