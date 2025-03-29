
import { useState } from "react";
import { Bell, Search, LogOut, User } from "lucide-react";
import { 
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

const Header = () => {
  const [searchOpen, setSearchOpen] = useState(false);

  return (
    <header className="bg-background/80 backdrop-blur-sm border-b border-slate-800 py-3 px-4 flex items-center justify-between">
      {searchOpen ? (
        <div className="flex-1 max-w-xl animate-fade-in">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size={18} />
            <input
              type="text"
              placeholder="Search..."
              className="w-full pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700 rounded-md focus:outline-none focus:ring-1 focus:ring-primary"
              autoFocus
              onBlur={() => setSearchOpen(false)}
            />
          </div>
        </div>
      ) : (
        <div className="flex-1">
          <button
            onClick={() => setSearchOpen(true)}
            className="text-slate-400 hover:text-slate-200 p-2 rounded-md hover:bg-slate-800/50"
          >
            <Search size={18} />
          </button>
        </div>
      )}
      
      <div className="flex items-center space-x-4">
        <button className="relative text-slate-400 hover:text-slate-200 p-2 rounded-md hover:bg-slate-800/50">
          <Bell size={18} />
          <span className="absolute top-1 right-1 w-2 h-2 bg-topup-green rounded-full"></span>
        </button>
        
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <button className="flex items-center space-x-2 text-sm font-medium bg-transparent hover:bg-slate-800/50 py-1 px-2 rounded-md">
              <div className="h-8 w-8 bg-topup-purple rounded-full flex items-center justify-center text-white">
                <User size={14} />
              </div>
              <span className="hidden sm:inline-block">Admin User</span>
            </button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" className="w-56">
            <DropdownMenuLabel>My Account</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem>
              <User className="mr-2" size={14} />
              <span>Profile</span>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <LogOut className="mr-2" size={14} />
              <span>Log out</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </header>
  );
};

export default Header;
