
import { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { 
  ChevronDown, 
  ChevronLeft, 
  ChevronRight, 
  Gauge, 
  Image, 
  LayoutGrid, 
  Package, 
  CreditCard, 
  Ticket, 
  UserCog, 
  FileText, 
  Settings
} from "lucide-react";
import ThemeToggle from "../ThemeToggle";
import { cn } from "@/lib/utils";

const sidebarItems = [
  { 
    name: "Dashboard", 
    icon: Gauge, 
    path: "/admin", 
    exact: true 
  },
  { 
    name: "Banners", 
    icon: Image, 
    path: "/admin/banners" 
  },
  { 
    name: "Categories", 
    icon: LayoutGrid, 
    path: "/admin/categories" 
  },
  { 
    name: "Products & Services", 
    icon: Package, 
    path: "/admin/products",
    children: [
      { name: "Products", path: "/admin/products" },
      { name: "Services", path: "/admin/services" },
      { name: "Profit Settings", path: "/admin/profit-settings" }
    ]
  },
  { 
    name: "Payment Gateways", 
    icon: CreditCard, 
    path: "/admin/payment-gateways" 
  },
  { 
    name: "Vouchers", 
    icon: Ticket, 
    path: "/admin/vouchers" 
  },
  { 
    name: "Users & Roles", 
    icon: UserCog, 
    path: "/admin/users" 
  },
  { 
    name: "Transactions", 
    icon: FileText, 
    path: "/admin/transactions" 
  },
  { 
    name: "Settings", 
    icon: Settings, 
    path: "/admin/settings" 
  }
];

interface SidebarProps {
  collapsed: boolean;
  setCollapsed: (collapsed: boolean) => void;
}

const Sidebar = ({ collapsed, setCollapsed }: SidebarProps) => {
  const location = useLocation();
  const [expandedItems, setExpandedItems] = useState<string[]>([]);

  const toggleExpand = (itemName: string) => {
    setExpandedItems(prev => 
      prev.includes(itemName) 
        ? prev.filter(item => item !== itemName) 
        : [...prev, itemName]
    );
  };

  const isActive = (path: string) => {
    if (path === "/admin" && location.pathname === "/admin") {
      return true;
    }
    return location.pathname.startsWith(path) && path !== "/admin";
  };

  return (
    <div className={cn(
      "sidebar h-screen bg-sidebar flex flex-col border-r border-slate-800 transition-all duration-300",
      collapsed ? "w-16" : "w-64"
    )}>
      <div className="flex items-center justify-between p-4 border-b border-slate-800">
        <h1 className={cn(
          "font-bold text-lg transition-all duration-300 bg-gradient-to-r from-topup-purple to-topup-green bg-clip-text text-transparent",
          collapsed ? "hidden" : "block"
        )}>
          Galaxy Admin
        </h1>
        <button 
          onClick={() => setCollapsed(!collapsed)}
          className="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-800"
        >
          {collapsed ? <ChevronRight size={20} /> : <ChevronLeft size={20} />}
        </button>
      </div>
      
      <div className="flex-1 overflow-y-auto py-2 space-y-1 px-2">
        {sidebarItems.map((item) => (
          <div key={item.name} className="relative">
            {item.children ? (
              <>
                <button
                  onClick={() => toggleExpand(item.name)}
                  className={cn(
                    "w-full sidebar-item",
                    isActive(item.path) && "active"
                  )}
                >
                  <item.icon size={18} />
                  {!collapsed && (
                    <>
                      <span className="flex-1 text-left">{item.name}</span>
                      <ChevronDown 
                        size={16} 
                        className={cn(
                          "transition-transform duration-200",
                          expandedItems.includes(item.name) ? "rotate-180" : ""
                        )}
                      />
                    </>
                  )}
                </button>
                {!collapsed && expandedItems.includes(item.name) && (
                  <div className="ml-8 mt-1 space-y-1">
                    {item.children.map((child) => (
                      <Link
                        key={child.path}
                        to={child.path}
                        className={cn(
                          "sidebar-item text-sm",
                          location.pathname === child.path && "active"
                        )}
                      >
                        <span>{child.name}</span>
                      </Link>
                    ))}
                  </div>
                )}
              </>
            ) : (
              <Link
                to={item.path}
                className={cn(
                  "sidebar-item",
                  (item.exact ? location.pathname === item.path : isActive(item.path)) && "active"
                )}
              >
                <item.icon size={18} />
                {!collapsed && <span>{item.name}</span>}
              </Link>
            )}
          </div>
        ))}
      </div>

      <div className="p-4 border-t border-slate-800">
        <div className="flex items-center">
          <div className={cn(
            "flex items-center space-x-2",
            collapsed ? "justify-center" : "justify-start"
          )}>
            <ThemeToggle />
            {!collapsed && (
              <div className="text-xs text-slate-500">v1.0.0</div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default Sidebar;
