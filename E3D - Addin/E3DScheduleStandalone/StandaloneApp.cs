using System;
using System.Collections;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using Aveva.Core.Database;
using Aveva.Core.Utilities.CommandLine;
using Aveva.Core.Utilities.Messaging;
using Aveva.E3D.Standalone;

namespace E3DScheduleStandalone
{
    class StandaloneApp
    {
        private static string projectCode;
        private static string mdb;
        private static int modNumber;
        private static List<string> arguments;

        static void Main(string[] args)
        {
            arguments = args.ToList();

            modNumber = Int32.Parse(arguments[0]);
            projectCode = arguments[1];
            mdb = arguments[2];

            arguments.RemoveAt(2);
            arguments.RemoveAt(1);
            arguments.RemoveAt(0);

            RunAPP();
        }

        private static void RunAPP()
        {
            try
            {
                Standalone.Start(modNumber);

                if (!Standalone.Open(projectCode, "SYSTEM", "XXXXXX", mdb, out PdmsMessage error))
                {
                    // error.Output();
                }
                else
                {
                    for (int i = 0; i < arguments.Count; i++)
                    {
                        Command.CreateCommand(arguments[i]).Run();
                    }

                    Project.CurrentProject.Close();
                    MDB.CurrentMDB.QuitWork();
                }

                Standalone.Finish();
            }
            catch (Exception ex)
            {
                Console.WriteLine(ex.ToString());
            }
        }
    }
}
