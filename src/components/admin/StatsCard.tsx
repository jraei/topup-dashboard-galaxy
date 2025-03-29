
import { ReactNode } from "react";
import { cn } from "@/lib/utils";

interface StatsCardProps {
  title: string;
  value: string | number;
  icon: ReactNode;
  trend?: {
    value: number;
    isPositive: boolean;
  };
  className?: string;
}

const StatsCard = ({ title, value, icon, trend, className }: StatsCardProps) => {
  return (
    <div className={cn("stats-card p-5", className)}>
      <div className="flex justify-between">
        <div>
          <p className="text-sm text-slate-400">{title}</p>
          <h3 className="text-2xl font-semibold mt-1">{value}</h3>
          
          {trend && (
            <div className="flex items-center mt-2">
              <div className={cn(
                "text-xs px-1.5 py-0.5 rounded flex items-center",
                trend.isPositive 
                  ? "text-green-400 bg-green-400/10" 
                  : "text-red-400 bg-red-400/10"
              )}>
                {trend.isPositive ? '+' : ''}{trend.value}%
              </div>
              <span className="text-xs text-slate-500 ml-2">vs last month</span>
            </div>
          )}
        </div>
        
        <div className="h-12 w-12 rounded-lg bg-topup-purple/20 flex items-center justify-center text-topup-purple">
          {icon}
        </div>
      </div>
    </div>
  );
};

export default StatsCard;
